<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * Cookie-urile care NU trebuie criptate.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
