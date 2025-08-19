<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Obtener los servicios específicos que pertenecen a esta categoría
     */
    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }
}
