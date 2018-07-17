<?php

use App\App;
use Slim\Views\Twig;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new App;

$container = $app->getContainer();

require __DIR__ . '/database.php';

require __DIR__ . '/../routes/web.php';

require __DIR__ . '/braintree.php';

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container->get(Twig::class)));