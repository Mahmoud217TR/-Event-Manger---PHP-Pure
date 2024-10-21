<?php

namespace App\Http\Middleware;

use Closure;
use App\Entities\IP;
use App\Http\Middleware;
use App\Services\IPService;

class DynamicAPIServiceMiddleware extends Middleware
{
    protected ?IPService $ips;
    protected ?IP $ip;

    public function __construct()
    {
        $this->ips = new IPService();
        $this->ip = $this->ips->findByIp(request()->getClientIp());
    }
    /**
     * Handle an incoming request.
     *
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Closure $next)
    {
        if (request()->getClientIp() != '127.0.0.1') {
            if ($this->isBlacklisted()) {
                response()
                    ->forbidden('Your IP has been blacklisted.')
                    ->json();
            }
    
            if (!$this->isWhitelisted()) {
                response()
                    ->forbidden('Your IP is not authorized')
                    ->json();
            }
        }

        $next();
    }

    /**
     * Check if the IP address is blacklisted.
     *
     * @return bool True if blacklisted, false otherwise.
     */
    protected function isBlacklisted(): bool
    {
        if ($this->ip) {
            return $this->ip->isBlacklisted();
        }
        return false;
    }

    /**
     * Check if the IP address is whitelisted.
     *
     * @return bool True if whitelisted, false otherwise.
     */
    protected function isWhitelisted(): bool
    {
        if ($this->ip) {
            return $this->ip->isWhitelisted();
        }
        return false;
    }
}
