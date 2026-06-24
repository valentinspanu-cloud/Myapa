<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaCitire extends Model
{
    protected $table    = 'rute_citire';
    protected $fillable = ['nume', 'activa'];
    protected $casts    = ['activa' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('activa', true);
    }
}
