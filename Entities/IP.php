<?php

namespace Entities;

use DateTime;

class IP extends Entity
{
    public string $ip_address;
    public bool $blacklisted;
    public DateTime $created_at;
    
    public function __construct(
        int $id,
        string $ip_address,
        bool $blacklisted,
        DateTime $created_at
    ) {
        $this->id = $id;
        $this->ip_address = $ip_address;
        $this->blacklisted = $blacklisted;
        $this->created_at = $created_at;
    }

    public static function fromRecord(array $record): static
    {
        return new static(
            $record['id'],
            $record['ip_address'],
            $record['blacklisted'],
            new DateTime($record['created_at']),
        );
    }

    public static function getTableName(): string
    {
        return 'ips';
    }

    public function isBlacklisted(): bool
    {
        return $this->blacklisted;
    }

    public function isWhitelisted(): bool
    {
        return !$this->blacklisted;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,
            'blacklisted' => $this->blacklisted,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}