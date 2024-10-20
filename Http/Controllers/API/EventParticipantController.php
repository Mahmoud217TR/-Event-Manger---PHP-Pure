<?php

namespace Http\Controllers\API;

use Http\Resources\EventParticipantResource;
use Http\Controllers\Controller;
use Http\Requests\API\EventParticipant\IndexRequest;
use Http\Requests\API\EventParticipant\StoreRequest;
use Services\EventParticipantService;

class EventParticipantController extends Controller
{
    protected EventParticipantService $service;
    
    public function __construct()
    {
        $this->service = new EventParticipantService();
    }

    public function index()
    {
        $request = IndexRequest::make();
        $request->validate();

        $eventParticipants = $this->service->get($request->filter());

        return response()
            ->code(200)
            ->json(EventParticipantResource::make($eventParticipants)->with(['participant', 'event']));
    }

    public function show(int $id)
    {
        $eventParticipant = $this->service->find($id);

        if (is_null($eventParticipant)) {
            return response()
                ->notFound()
                ->json();
        }

        return response()->json(
            EventParticipantResource::make($eventParticipant)->with(['participant', 'event'])
        );
    }

    public function store()
    {
        $request = StoreRequest::make();
        $request->validate();

        $eventParticipant = $this->service->create(
            $request->getEvent(),
            $request->getParticipant(),
        );

        return response()
            ->code(201)
            ->json([
                'message' => 'EventParticipant created successfully',
                'eventParticipant' => EventParticipantResource::make($eventParticipant)->with(['participant', 'event'])
            ]);
    }

    public function destroy(int $id)
    {
        $eventParticipant = $this->service->find($id);
  
        if (is_null($eventParticipant)) {
            return response()
                ->notFound()
                ->json();
        }

        $eventParticipant = $this->service->delete($eventParticipant);

        return response()
            ->code(200)
            ->json([
                'message' => 'EventParticipant deleted successfully',
            ]);
    }
}
