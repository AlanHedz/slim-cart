<?php

use App\Basket\Basket;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Address;
use App\Models\Payment;
use App\Support\Storage\Contracts\StorageInterface;
use App\Support\Storage\SessionStorage;
use App\Validation\Contracts\ValidatorInterface;
use App\Validation\Validator;
use Psr\Container\ContainerInterface;
use Slim\Router;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use function DI\get;


return [
		'router' => get(Router::class),
		ValidatorInterface::class => function (ContainerInterface $c) {
			return new Validator;
		},
		StorageInterface::class => function (ContainerInterface $c) {
			return new SessionStorage('cart');
		},
		Twig::class => function (ContainerInterface $c) {
			$twig = new Twig(__DIR__ . '/../resources/views', [
				'cache' => false
			]);

			$twig->addExtension(new TwigExtension(
				$c->get('router'),
				$c->get('request')->getUri()
			));

			$twig->getEnvironment()->addGlobal('basket', $c->get(Basket::class));

			return $twig;
		},
        Product::class => function (ContainerInterface $c) {
            return new Product;
        },
        Order::class => function (ContainerInterface $c) {
            return new Order;
        },
        Customer::class => function (ContainerInterface $c) {
            return new Customer;
        },
        Address::class => function (ContainerInterface $c) {
            return new Address;
        },
        Payment::class => function (ContainerInterface $c) {
            return new Payment;
        },
		Basket::class => function (ContainerInterface $c) {
			return new Basket(
				$c->get(SessionStorage::class),
				$c->get(Product::class)
			);
		},

];