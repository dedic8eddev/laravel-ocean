<?php

namespace App\Domain\Repositories;

use App\Domain\Models\Cargo;
use App\Domain\Queries\CargoTableQuery;

/**
 * Class CargoRepository.
 */
class CargoRepository extends BaseRepository {

    public function table(array $params) {
        return CargoTableQuery::create($params)->apply();
    }

    public function storeCargo(array $data) {
        $cargo = new Cargo($data);
        $cargo->save();

        if (isset($data['organization_ids'])) {
            $cargo->whitelistOrganizations($data['organization_ids']);
        }

        return $cargo;
    }

    public function updateCargo(Cargo $cargo, array $data) {
        $cargo->fill($data);
        $cargo->save();

        if (isset($data['organization_ids'])) {
            $cargo->removeBlacklist();
            $cargo->whitelistOrganizations($data['organization_ids']);
        }

        return $cargo;
    }
}
