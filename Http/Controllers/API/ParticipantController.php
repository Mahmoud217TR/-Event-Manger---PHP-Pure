<?php

namespace Http\Controllers\API;

use Http\Controllers\Controller;
use Http\Requests\API\Participant\IndexRequest;
use Http\Requests\API\Participant\StoreRequest;
use Http\Requests\API\Participant\UpdateRequest;
use Http\Resources\ParticipantResource;
use Services\ParticipantService;

class ParticipantController extends Controller
{
    protected ParticipantService $service;
    
    public function __construct()
    {
        $this->service = new ParticipantService();
    }

    public function index()
    {
        $request = IndexRequest::make();
        $request->validate();

        $participants = $this->service->get($request->filter());

        return response()
            ->code(200)
            ->json(ParticipantResource::make($participants));
    }

    public function show(int $id)
    {
        $participant = $this->service->find($id);
  
        if (is_null($participant)) {
            return response()
                ->notFound()
                ->json();
        }

        return response()->json(ParticipantResource::make($participant));
    }

    public function store()
    {
        $request = StoreRequest::make();
        $request->validate();

        $participant = $this->service->create(
            $request->get('name'),
            $request->get('email'),
        );

        return response()
            ->code(201)
            ->json([
                'message' => 'Participant created successfully',
                'participant' => ParticipantResource::make($participant)
            ]);
    }

    public function update(int $id)
    {
        $participant = $this->service->find($id);
  
        if (is_null($participant)) {
            return response()
                ->notFound()
                ->json();
        }

        $request = UpdateRequest::make();
        $request->validate();

        $participant = $this->service->update(
            $participant,
            $request->get('name'),
            $request->get('email'),
        );

        return response()
            ->code(200)
            ->json([
                'message' => 'Participant updated successfully',
                'participant' => ParticipantResource::make($participant)
            ]);
    }

    public function destroy(int $id)
    {
        $participant = $this->service->find($id);
  
        if (is_null($participant)) {
            return response()
                ->notFound()
                ->json();
        }

        $participant = $this->service->delete($participant);

        return response()
            ->code(200)
            ->json([
                'message' => 'Participant deleted successfully',
            ]);
    }
}
