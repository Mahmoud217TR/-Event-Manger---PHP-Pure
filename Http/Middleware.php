<?php

namespace Http;

use Closure;
use Http\Request;

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
