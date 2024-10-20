<?php

namespace App\Http\Requests\API\Location;

use App\Filters\LocationFilter;
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
            ->string('address')
            ->boolean('available');
    }

    public function filter(): LocationFilter
    {
        return LocationFilter::make()
            ->when(
                $this->has('name'),
                fn (LocationFilter $filter) => $filter->whereName($this->get('name'))
            )
            ->when(
                $this->has('address'),
                fn (LocationFilter $filter) => $filter->whereAddress($this->get('address'))
            )
            ->when(
                $this->has('available') && $this->boolean('available'),
                fn (LocationFilter $filter) => $filter->whereAvailable()
            );
    }
}
