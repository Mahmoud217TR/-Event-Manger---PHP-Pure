<?php

namespace Services;

use DateTime;
use Entities\IP;
use Filters\IPFilter;
use Repositories\Database\DatabaseIPRepository;
use Repositories\IPRepository;

class IPService
{
    protected IPRepository $ips;
    
    public function __construct()
    {
        $this->ips = new DatabaseIPRepository();
    }

    /**
     * Get all ips based on optional filters.
     *
     * @param IPFilter|null $filter The filter to apply (optional).
     * @return array<IP>
     */
    public function get(IPFilter $filter = null): array
    {
        $conditions = [];
        if ($filter) {
            $conditions = $filter->build();
        }

        return $this->ips->get($conditions);
    }

    /**
     * Get an ips based on id.
     *
     * @param int $id The ip id.
     * @return IP
     */
    public function find(int $id): ?IP
    {
        return $this->ips->find($id);
    }

    /**
     * Get an ip based on ip address.
     *
     * @param int $ip_address The ip address.
     * @return IP
     */
    public function findByIp(string $ip_address): ?IP
    {
        $ips = $this->ips->get(
            IPFilter::make()
                ->whereIPAddress($ip_address)
                ->build()
        );
        return isset($ips[0]) ? $ips[0] : null;
    }
    
    /**
     * Create a new ip.
     * 
     * @param string $ip_address The ip address
     * @param bool $blacklisted Whether the ip is Blacklisted or Whitelisted
     * @return IP
     */
    public function create(string $ip_address, bool $blacklisted): IP
    {
        return $this->ips->create([
            'ip_address' => $ip_address,
            'blacklisted' => $blacklisted?1:0,
        ]);
    }

    /**
     * Delete an existing ip.
     *
     * @param IP $ip The ip to be deleted
     * @return bool
     */
    public function delete(IP $ip): bool
    {
        return $this->ips->delete($ip->id);
    }
}
