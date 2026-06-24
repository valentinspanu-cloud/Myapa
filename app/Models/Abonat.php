<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abonat extends Model
{
    protected $table = 'abonati';

    protected $fillable = [
        'cod_abonat', 'id_client', 'id_locatie',
        'nume_abonat', 'adresa', 'localitate',
        'strada', 'nr_strada', 'bloc', 'addr_stair', 'addr_apt',
        'telefon', 'nr_contract', 'ruta', 'sector',
        'sincronizat_la',
    ];

    protected $casts = [
        'sincronizat_la' => 'datetime',
    ];
}
