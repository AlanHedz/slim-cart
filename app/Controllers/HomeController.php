<?php

namespace App\Controllers;

use App\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController 
{
	public function index(Request $request, Response $response, Twig $view)
	{
		$products = Product::all();
		return $view->render($response, 'home.twig', ['products' => $products]);
	}
}