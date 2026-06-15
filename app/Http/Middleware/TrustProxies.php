<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // L11: fideloper/proxy eliminat — folosim clasa built-in din Laravel

    /**
     * Proxy-urile de încredere pentru aplicație.
     * Setează '*' dacă ești în spatele unui load balancer (ex: nginx proxy).
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * Header-ele folosite pentru detectarea proxy-urilor.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
