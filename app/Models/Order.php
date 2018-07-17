<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Address;
use App\Models\Product;
use App\Models\Payment;

class Order extends Model
{
    protected $fillable = ['hash', 'total', 'paid', 'address_id'];

    public function address ()
    {
        return $this->belongsTo(Address::class);
    }

    public function products ()
    {
        return $this->belongsToMany(Product::class, 'orders_products')->withPivot('quantity');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

}