<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([

	'driver' => 'yourdriver',
	'host' => 'yourhost',
	'database' => 'youtdatabase',
	'username' => 'username',
	'password' => 'yourpassword',
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();