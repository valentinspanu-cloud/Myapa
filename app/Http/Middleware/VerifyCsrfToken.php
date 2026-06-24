<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URL-urile excluse din verificarea CSRF.
     * Adaugă aici endpoint-urile de webhook extern (ex: plăți).
     *
     * @var array<int, string>
     */
    protected $except = [
        'facturi/inregistrare-tranzactie', // webhook plată externă
    ];
}
