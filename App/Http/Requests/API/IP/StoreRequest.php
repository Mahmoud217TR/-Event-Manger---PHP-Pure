<?php

namespace App\Http\Requests\API\IP;

use App\Http\Requests\API\APIFormRequest;
use App\Http\Validator;
use App\Services\IPService;

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
            ->required('ip_address')
            ->ip('ip_address')
            ->required('is_blacklisted')
            ->boolean('is_blacklisted')
            ->unique('ip_address', 'ips', 'ip_address', null, null, static::getUniqeMessage());
    }

    public static function getUniqeMessage(): string
    {
        $ip_address = request()->get('ip_address');
        if ($ip_address) {
            $ip = (new IPService())->findByIp($ip_address);
            if ($ip) {
                return static::getMessage($ip->isBlacklisted(), request()->boolean('is_blacklisted'));
            }
        }
        return '';
    }

    public static function getMessage(bool $isBlacklisted, bool $toBeBlacklisted): string
    {
        if ($isBlacklisted) {
            if ($toBeBlacklisted) {
                return "The IP address already blacklisted.";
            } else {
                return "The IP is blacklisted, please remove it from blacklist first.";
            }
        } else {
            if ($toBeBlacklisted) {
                return "The IP is whitelisted, please remove it from whitelist first.";
                
            } else {
                return "The IP address already whitelisted.";
            }
        }
    }
}
