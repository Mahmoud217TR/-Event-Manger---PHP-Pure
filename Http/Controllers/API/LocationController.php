<?php

namespace Http\Controllers\API;

use Http\Controllers\Controller;
use Http\Requests\API\Location\IndexRequest;
use Http\Requests\API\Location\StoreRequest;
use Http\Requests\API\Location\UpdateRequest;
use Http\Resources\LocationResource;
use Services\LocationService;

class LocationController extends Controller
{
    protected LocationService $service;
    
    public function __construct()
    {
        $this->service = new LocationService();
    }

    public function index()
    {
        $request = IndexRequest::make();
        $request->validate();

        $locations = $this->service->get($request->filter());

        return response()
            ->code(200)
            ->json(LocationResource::make($locations));
    }

    public function show(int $id)
    {
        $location = $this->service->find($id);
  
        if (is_null($location)) {
            return response()
                ->notFound()
                ->json();
        }

        return response()->json(LocationResource::make($location));
    }

    public function store()
    {
        $request = StoreRequest::make();
        $request->validate();

        $location = $this->service->create(
            $request->get('name'),
            $request->get('address'),
            $request->get('capacity')
        );

        return response()
            ->code(201)
            ->json([
                'message' => 'Location created successfully',
                'location' => LocationResource::make($location)
            ]);
    }

    public function update(int $id)
    {
        $location = $this->service->find($id);
  
        if (is_null($location)) {
            return response()
                ->notFound()
                ->json();
        }

        $request = UpdateRequest::make();
        $request->validate();

        $location = $this->service->update(
            $location,
            $request->get('name'),
            $request->get('address'),
            $request->get('capacity')
        );

        return response()
            ->code(200)
            ->json([
                'message' => 'Location updated successfully',
                'location' => LocationResource::make($location)
            ]);
    }

    public function destroy(int $id)
    {
        $location = $this->service->find($id);
  
        if (is_null($location)) {
            return response()
                ->notFound()
                ->json();
        }

        $location = $this->service->delete($location);

        return response()
            ->code(200)
            ->json([
                'message' => 'Location deleted successfully',
            ]);
    }
}
