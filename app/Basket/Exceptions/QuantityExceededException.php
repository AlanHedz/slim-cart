<?php

namespace App\Basket\Exceptions;

use Exception;

class QuantityExceededException extends Exception
{
	protected $message = 'Has añadido el maximo de stock de este producto.';
}