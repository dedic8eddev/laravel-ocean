<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Port;
use App\Domain\Models\Organization;
use App\Domain\Queries\PortTableQuery;

/**
 * Class PortRepository.
 */
class PortRepository extends BaseRepository
{
    public function table(array $params) {
        return PortTableQuery::create($params)->apply();
    }

    public function storePort(array $data) {
        $port = new Port($data);
        $port->save();

        if (isset($data['organization_ids'])) {
            $all_organization_ids = Organization::all()->pluck('id')->toArray();
            $blacklist = array_diff($all_organization_ids, $data['organization_ids']);

            $port->addToBlacklist($blacklist);
        }

        return $port;
    }

    public function updatePort(Port $port, array $data) {
        $port->fill($data);
        $port->save();

        if (isset($data['organization_ids'])) {
            $all_organization_ids = Organization::all()->pluck('id')->toArray();
            $blacklist = array_diff($all_organization_ids, $data['organization_ids']);

            $port->removeBlacklist();
            $port->addToBlacklist($blacklist);
        }

        return $port;
    }
}
