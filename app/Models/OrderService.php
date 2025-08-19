<?php
// app/Models/OrderService.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'service_item_id',
        'service_category_id',
        'service_name',
        'price',
        'subtotal',
        'quantity',
        'description'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function serviceItem()
    {
        return $this->belongsTo(ServiceItem::class);
    }
    
    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
