<?php
// app/Models/State.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = ['type','name','description'];

    public function ordersAsStatus()
    {
        return $this->hasMany(Order::class, 'order_state_id');
    }

    public function ordersAsPayment()
    {
        return $this->hasMany(Order::class, 'payment_state_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'status_id');
    }
}
