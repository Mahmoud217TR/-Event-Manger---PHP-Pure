<?php

namespace App\Http\Requests\API\Event;

use App\Filters\EventFilter;
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
            ->date('before_date')
            ->date('after_date')
            ->date('date')
            ->integer('location_id');
    }

    public function filter(): EventFilter
    {
        return EventFilter::make()
            ->when(
                $this->has('name'),
                fn (EventFilter $filter) => $filter->whereName($this->get('name'))
            )
            ->when(
                $this->has('before_date'),
                fn (EventFilter $filter) => $filter->whereBeforeDate($this->get('before_date'))
            )
            ->when(
                $this->has('after_date'),
                fn (EventFilter $filter) => $filter->whereAfterDate($this->get('after_date'))
            )
            ->when(
                $this->has('date'),
                fn (EventFilter $filter) => $filter->whereDate($this->get('date'))
            )
            ->when(
                $this->has('location_id'),
                fn (EventFilter $filter) => $filter->whereLocation($this->get('location_id'))
            );
    }
}
