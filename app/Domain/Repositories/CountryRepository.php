<?php

namespace App\Domain\Repositories;

use App\Domain\Queries\CountryTableQuery;
/**
 * Class CountryRepository.
 */
class CountryRepository extends BaseRepository
{
    /**
     * Get countries.
     *
     * @param array $params
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function table(array $params) {
        return CountryTableQuery::create($params)->apply();
    }
}
