<?php

namespace App\Controllers;

use App\Models\Product;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use Slim\Views\Twig;


class ProductController
{

	public function show($slug, Request $request, Response $response, Twig $view, Router $router)
	{
		$product = Product::where('slug', $slug)->first();

		if (!$product) {
			return $response->withRedirect($router->pathFor('home'));			
		}

		return $view->render($response, 'products/show.twig', ['product' => $product]);
	}
}