<?php

namespace App\Http\Requests\API\Participant;

use App\Filters\ParticipantFilter;
use App\Http\Requests\API\APIFormRequest;
use App\Http\Validator;

class IndexRequest extends APIFormRequest
{
    /**
     * Create a new Validator instance for this request's data.
     *
     * @return Validator
     */
    public static function validator(array $data): Validator
    {
        return Validator::make($data)
            ->string('name')
            ->email('email');
    }

    public function filter(): ParticipantFilter
    {
        return ParticipantFilter::make()
            ->when(
                $this->has('name'),
                fn (ParticipantFilter $filter) => $filter->whereName($this->get('name'))
            )
            ->when(
                $this->has('email'),
                fn (ParticipantFilter $filter) => $filter->whereEmail($this->get('email'))
            );
    }
}
