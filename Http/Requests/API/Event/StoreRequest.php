<?php

namespace Http\Requests\API\Event;

use DateTime;
use Http\Requests\API\APIFormRequest;
use Http\Validator;

class StoreRequest extends APIFormRequest
{
    /**
     * Create a new Validator instance for this request's data.
     *
     * @return Validator
     */
    public static function validator(array $data): Validator
    {
        return Validator::make($data)
            ->required('name')
            ->string('name')
            ->required('date')
            ->date('date')
            ->required('location_id')
            ->integer('location_id')
            ->exists('location_id', 'locations')
            ->uniqueOn(
                ['location_id', 'date'],
                'events',
                ['location_id', 'date'],
                null,
                null,
                "Location reserved for this date"
            );
    }

    public function getDate(): DateTime
    {
        return new DateTime($this->get('date'));
    }
}
