<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StradaGeocodata extends Model
{
    protected $table = 'strazi_geocodate';

    protected $fillable = [
        'ruta', 'nume_strada', 'localitate', 'geojson', 'geocodat_la',
    ];

    protected $casts = [
        'geocodat_la' => 'datetime',
    ];
}
