<?php
// app/Models/Ticket.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','state_id','service_item_id','printed_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function serviceItem()
    {
        return $this->belongsTo(ServiceItem::class, 'service_item_id');
    }
}
