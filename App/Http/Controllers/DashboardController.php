<?php

namespace App\Http\Controllers;

use App\Filters\IPFilter;
use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\IPService;

class DashboardController extends Controller
{
    protected EventService $events;
    protected IPService $ips;
    
    public function __construct()
    {
        $this->events = new EventService();
        $this->ips = new IPService();
    }

    public function invoke()
    {
        $events = $this->events->get();
        $ips = $this->ips->get(IPFilter::make()->whereBlacklisted());

        return response()
            ->code(200)
            ->view('index', [
                'events' => $events,
                'ips' => $ips,
            ]);
    }
}
