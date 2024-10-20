<?php

namespace App\Http;

use Closure;

abstract class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure $next
     * @return mixed
     */
    abstract public function handle(Closure $next);
}
