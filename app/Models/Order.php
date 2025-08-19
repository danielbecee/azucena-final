<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id','order_date','due_date',
        'order_state_id','payment_state_id',
        'total_amount','paid_amount','notes'
    ];
    
    protected $casts = [
        'order_date' => 'datetime',
        'due_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // Relaciones
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderState()
    {
        return $this->belongsTo(State::class, 'order_state_id');
    }

    public function paymentState()
    {
        return $this->belongsTo(State::class, 'payment_state_id');
    }

    // La relación orderItems ha sido eliminada

    public function services()
    {
        return $this->hasMany(OrderService::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    // Métodos útiles
    
    /**
     * Obtener todos los items del pedido (solo servicios)
     */
    public function getAllItems()
    {
        $items = [];
        
        // Obtener servicios
        foreach ($this->services as $service) {
            $items[] = [
                'type' => 'service',
                'id' => $service->service_item_id,
                'name' => $service->service_name ?? 'Servicio no disponible',
                'price' => $service->price,
                'subtotal' => $service->subtotal,
                'description' => $service->description ?? '',
            ];
        }
        
        return collect($items);
    }
    
    /**
     * Verifica si el pedido está vencido
     */
    public function isOverdue()
    {
        return $this->due_date < Carbon::today();
    }
    
    /**
     * Calcula el balance pendiente
     */
    public function getBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
    
    /**
     * Verifica si el pedido está completamente pagado
     */
    public function isFullyPaid()
    {
        return $this->paid_amount >= $this->total_amount;
    }
    
    /**
     * Actualiza el estado de pago basado en el monto pagado
     */
    public function updatePaymentState()
    {
        if ($this->paid_amount >= $this->total_amount) {
            $this->payment_state_id = 3; // Pagado
        } elseif ($this->paid_amount > 0) {
            $this->payment_state_id = 2; // Parcial
        } else {
            $this->payment_state_id = 1; // Pendiente
        }
        
        return $this->save();
    }
}
