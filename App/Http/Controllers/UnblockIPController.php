<?php

namespace App\Http\Controllers;

use App\Filters\IPFilter;
use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\IPService;

class UnblockIPController extends Controller
{
    protected IPService $ips;
    
    public function __construct()
    {
        $this->ips = new IPService();
    }

    public function invoke(int $id)
    {
        $ip = $this->ips->find($id);
        if ($ip) {
            $this->ips->delete($ip);
        }

        return response()
            ->redirect('/');
    }
}
