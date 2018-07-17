<?php
$app->get('/', ['App\Controllers\HomeController', 'index'])->setName('home');

//Product Routes
$app->get('/products/{slug}', ['App\Controllers\ProductController', 'show'])->setName('product.show');

//Car Routes
$app->get('/cart', ['App\Controllers\CartController', 'index'])->setName('cart.index');
$app->get('/cart/add/{slug}/{quantity}', ['App\Controllers\CartController', 'add'])->setName('cart.add');
$app->post('/cart/update/{slug}/', ['App\Controllers\CartController', 'update'])->setName('cart.update');

//Orders Routes
$app->get('/order', ['App\Controllers\OrderController', 'index'])->setName('order.index');
$app->post('/order', ['App\Controllers\OrderController', 'create'])->setName('order.create');
$app->get('/order/{hash}', ['App\Controllers\OrderController', 'show'])->setName('order.show');

$app->get('/braintree/token', ['App\Controllers\BraintreeController', 'token'])->setName('braintree.token');