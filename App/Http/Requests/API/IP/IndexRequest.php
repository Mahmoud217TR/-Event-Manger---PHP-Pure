<?php

namespace App\Http\Requests\API\IP;

use App\Filters\IPFilter;
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
            ->string('ip_address')
            ->boolean('blacklisted')
            ->boolean('whiteliste');
    }

    public function filter(): IPFilter
    {
        return IPFilter::make()
            ->when(
                $this->has('ip_address'),
                fn (IPFilter $filter) => $filter->whereIPAddress($this->get('ip_address'))
            )
            ->when(
                $this->boolean('blacklisted'),
                fn (IPFilter $filter) => $filter->whereBlacklisted()
            )
            ->when(
                $this->boolean('whiteliste'),
                fn (IPFilter $filter) => $filter->whereWhitelisted()
            );
    }
}
