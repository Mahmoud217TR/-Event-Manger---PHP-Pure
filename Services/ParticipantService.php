<?php

namespace Services;

use Entities\Participant;
use Filters\ParticipantFilter;
use Repositories\Database\DatabaseParticipantRepository;
use Repositories\ParticipantRepository;

class ParticipantService
{
    protected ParticipantRepository $participants;
    
    public function __construct()
    {
        $this->participants = new DatabaseParticipantRepository();
    }

    /**
     * Get all participants based on optional filters.
     *
     * @param ParticipantFilter|null $filter The filter to apply (optional).
     * @return array<Participant>
     */
    public function get(ParticipantFilter $filter = null): array
    {
        $conditions = [];
        if ($filter) {
            $conditions = $filter->build();
        }
        return $this->participants->get($conditions);
    }

    /**
     * Get an participants based on id.
     *
     * @param int $id The event id.
     * @return Participant
     */
    public function find(int $id): ?Participant
    {
        return $this->participants->find($id);
    }
    
    /**
     * Create a new participant.
     * 
     * @param string $name The participant name
     * @param string $email The participant email
     * @return Participant
     */
    public function create(
        string $name,
        string $email
    ): Participant {
        return $this->participants->create([
            'name' => $name,
            'email' => $email,
        ]);
    }

    /**
     * Update an existing participant.
     * 
     * @param Participant $participant The participant to be updated
     * @param string $name The participant name
     * @param string $email The participant email
     * @return Participant
     */
    public function update(
        Participant $participant,
        string $name,
        string $email,
    ): Participant {
        $this->participants->update(
            $participant->id,
            [
                'name' => $name,
                'email' => $email,
            ]
        );

        return $participant->fresh();
    }

    /**
     * Delete an existing participant.
     *
     * @param Participant $participant The participant to be deleted
     * @return bool
     */
    public function delete(Participant $participant): bool
    {
        return $this->participants->delete($participant->id);
    }
}
