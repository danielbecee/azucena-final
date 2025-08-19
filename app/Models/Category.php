<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
