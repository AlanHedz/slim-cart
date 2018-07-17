<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    public $quantity = null;

	public function hasLowStock()
	{
		if ($this->outOfStock()) {
			return false;
		}

		return (bool) ($this->stock <= 5);
	}

	public function outOfStock()
	{
		return $this->stock == 0;
	}

	public function inStock()
	{
		return $this->stock >= 1;
	}

	public function hasStock($quantity)
	{
		return $this->stock >= $quantity;
	}

	public function orders ()
    {
        return $this->belongsToMany(Order::class, 'order_products')->withPivot('quantity');
    }

}