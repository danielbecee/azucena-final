<?php
// app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','product_id','quantity','unit_price','description'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'related_item_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
