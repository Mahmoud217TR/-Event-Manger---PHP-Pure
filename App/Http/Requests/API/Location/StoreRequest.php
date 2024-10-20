<?php

namespace App\Http\Requests\API\Location;

use DateTime;
use App\Http\Requests\API\APIFormRequest;
use App\Http\Validator;

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
            ->required('address')
            ->string('address')
            ->required('capacity')
            ->integer('capacity');
    }
}
