<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceExternClient extends Model
{
    protected $table = 'invoice_extern_clients';

    protected $fillable = [
        'cod_client',
        'contract_nr',
        'client_id',
        'nume',
        'email',
    ];
}
