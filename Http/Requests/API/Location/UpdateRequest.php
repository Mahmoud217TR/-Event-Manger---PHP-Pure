<?php

namespace Http\Requests\API\Location;

use DateTime;
use Http\Requests\API\APIFormRequest;
use Http\Validator;

class UpdateRequest extends APIFormRequest
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
            ->required('address')
            ->string('address')
            ->required('capacity')
            ->integer('capacity');
    }
}
