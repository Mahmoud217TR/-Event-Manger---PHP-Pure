<?php

namespace Http\Controllers\API;

use Http\Resources\IPResource;
use Http\Controllers\Controller;
use Http\Requests\API\IP\IndexRequest;
use Http\Requests\API\IP\StoreRequest;
use Services\IPService;

class IPController extends Controller
{
    protected IPService $service;
    
    public function __construct()
    {
        $this->service = new IPService();
    }

    public function index()
    {
        $request = IndexRequest::make();
        $request->validate();

        $ips = $this->service->get($request->filter());

        return response()
            ->code(200)
            ->json(IPResource::make($ips));
    }

    public function show(int $id)
    {
        $ip = $this->service->find($id);

        if (is_null($ip)) {
            return response()
                ->notFound()
                ->json();
        }

        return response()->json(IPResource::make($ip));
    }

    public function store()
    {
        $request = StoreRequest::make();
        $request->validate();

        $ip = $this->service->create(
            $request->get('ip_address'),
            $request->boolean('is_blacklisted'),
        );

        return response()
            ->code(201)
            ->json([
                'message' => $ip->isBlacklisted() ? 'IP blacklisted successfully' : 'IP whitelisted successfully',
                'ip' => IPResource::make($ip)
            ]);
    }

    public function destroy(int $id)
    {
        $ip = $this->service->find($id);
  
        if (is_null($ip)) {
            return response()
                ->notFound()
                ->json();
        }

        $ip = $this->service->delete($ip);

        return response()
            ->code(200)
            ->json([
                'message' => 'IP deleted successfully',
            ]);
    }
}
