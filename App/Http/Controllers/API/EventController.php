<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\EventResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Event\IndexRequest;
use App\Http\Requests\API\Event\StoreRequest;
use App\Http\Requests\API\Event\UpdateRequest;
use App\Services\EventService;

class EventController extends Controller
{
    protected EventService $service;
    
    public function __construct()
    {
        $this->service = new EventService();
    }

    public function index()
    {
        $request = IndexRequest::make();
        $request->validate();

        $events = $this->service->get($request->filter());

        return response()
            ->code(200)
            ->json(EventResource::make($events)->with(['location','visitors']));
    }

    public function show(int $id)
    {
        $event = $this->service->find($id);

        if (is_null($event)) {
            return response()
                ->notFound()
                ->json();
        }

        return response()->json(
            EventResource::make($event)->with(['location', 'participants'])
        );
    }

    public function store()
    {
        $request = StoreRequest::make();
        $request->validate();

        $event = $this->service->create(
            $request->get('name'),
            $request->getDate(),
            $request->get('location_id')
        );

        return response()
            ->code(201)
            ->json([
                'message' => 'Event created successfully',
                'event' => EventResource::make($event)->with(['location'])
            ]);
    }

    public function update(int $id)
    {
        $event = $this->service->find($id);
  
        if (is_null($event)) {
            return response()
                ->notFound()
                ->json();
        }

        $request = UpdateRequest::make();
        $request->validate();

        $event = $this->service->update(
            $event,
            $request->get('name'),
            $request->getDate(),
            $request->get('location_id')
        );

        return response()
            ->code(200)
            ->json([
                'message' => 'Event updated successfully',
                'event' => EventResource::make($event)->with(['location'])
            ]);
    }

    public function destroy(int $id)
    {
        $event = $this->service->find($id);
  
        if (is_null($event)) {
            return response()
                ->notFound()
                ->json();
        }

        $event = $this->service->delete($event);

        return response()
            ->code(200)
            ->json([
                'message' => 'Event deleted successfully',
            ]);
    }
}
