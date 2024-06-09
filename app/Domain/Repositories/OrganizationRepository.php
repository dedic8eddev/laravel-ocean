<?php

namespace App\Domain\Repositories;

use App\Domain\Queries\OrganizationTableQuery;

/**
 * Class OrganizationRepository.
 */
class OrganizationRepository extends BaseRepository
{
    public function table(array $params) {
        return OrganizationTableQuery::create($params)->apply();
    }
}
