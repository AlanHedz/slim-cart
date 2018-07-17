<?php

namespace App\Controllers;

use App\Basket\Basket;
use App\Events\OrderWasCreated;
use App\Handlers\EmptyBasket;
use App\Handlers\MakeOrderPaid;
use App\Handlers\RecordFailedPayment;
use App\Handlers\RecordSuccessfulPayment;
use App\Handlers\UpdateStock;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Validation\Contracts\ValidatorInterface;
use App\Validation\Forms\OrderForm;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Router;
use Slim\Views\Twig;

class OrderController
{

    protected $basket;

    protected $router;

    protected $validator;

    function __construct(Basket $basket, Router $router, ValidatorInterface $validator)
    {
        $this->basket = $basket;
        $this->router = $router;
        $this->validator = $validator;
    }

    public function index(Request $request, Response $response, Twig $view)
    {
        $this->basket->refresh();


        if (!$this->basket->subTotal()) {
            return $response->withRedirect($this->router->pathFor('cart.index'));
        }

        return $view->render($response, 'orders/index.twig');
    }

    public function show($hash, Request $request, Response $response, Twig $view, Order $order)
    {
        $order = $order->with(['address', 'products'])->where('hash', $hash)->first();

        if (!$order) {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        return $view->render($response, 'orders/show.twig', ['order' => $order]);
    }

    public function create(Request $request, Response $response, Customer $customer, Address $address)
    {
        $this->basket->refresh();

        if (!$request->getParam('payment_method_nonce')) {
            return $response->withRedirect($this->router->pathFor('order.index'));
        }

        if (!$this->basket->subTotal()) {
            return $response->withRedirect($this->router->pathFor('cart.index'));
        }
        $validation = $this->validator->validate($request, OrderForm::rules());

        if ($validation->fails()) {
            return $response->withRedirect($this->router->pathFor('order.index'));
        }

        $hash = bin2hex(random_bytes(32));

        $customer = $customer->firstOrCreate([
            'email' => $request->getParam('email'),
            'name' => $request->getParam('name')
        ]);

        $address = $address->firstOrCreate([
            'address1' => $request->getParam('address1'),
            'address2' => $request->getParam('address2'),
            'city' => $request->getParam('city'),
            'postal_code' => $request->getParam('postal_code'),

        ]);

        $order = $customer->orders()->create([
            'hash' => $hash,
            'paid' => false,
            'total' => $this->basket->subTotal() + 10,
            'address_id' => $address->id
        ]);

        $order->products()->saveMany($this->basket->all(), $this->getQuantities($this->basket->all()));

        $result = \Braintree_Transaction::sale([
            'amount' => $this->basket->subTotal() + 10,
            'paymentMethodNonce' => $request->getParam('payment_method_nonce'),
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        $event = new OrderWasCreated($order, $this->basket);

        if (!$result->success) {
            $event->attach( new RecordFailedPayment());
            $event->dispatch();

            return $response->withRedirect($this->router->pathFor('order.index'));
        }


        $event->attach([
            new MakeOrderPaid,
            new RecordSuccessfulPayment($result->transaction->id),
            new UpdateStock,
            new EmptyBasket,
        ]);

        $event->dispatch();

        return $response->withRedirect($this->router->pathFor('order.show', [
            'hash' => $hash
        ]));

    }

    protected function getQuantities($items)
    {
        $quantities = [];

        foreach ($items as $item) {
            $quantities[] = ['quantity' => $item->quantity];
        }

        return $quantities;

    }
}