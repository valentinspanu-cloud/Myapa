<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientCode extends Model
{
    protected $fillable = [
        'user_id', 'client_code', 'contract_nr', 'client_id'
    ];
}
