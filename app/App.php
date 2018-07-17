<?php

namespace App;
use DI\Bridge\Slim\App as DIBridge;
use DI\ContainerBuilder;

class App extends DIBridge
{
	protected function configureContainer (ContainerBuilder $builder)
	{

		$builder->addDefinitions([
            'settings.displayErrorDetails' => true,
		]);

		$builder->addDefinitions(__DIR__ . '/config.php');
	}
}