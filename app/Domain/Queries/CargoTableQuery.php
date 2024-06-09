<?php

namespace App\Domain\Queries;

use App\Domain\Models\Cargo;
use App\Http\Resources\CargoResource;
use WebThatMatters\DynamicFilter\DynamicFilter;
use WebThatMatters\DynamicFilter\Filters\StringFilter;

class CargoTableQuery extends DynamicFilter {

    function baseQuery() {
        return Cargo::query();
    }

    public function filters() {
        $this->filter('name', new StringFilter());
        $this->filter('description', new StringFilter());
    }

    public function sortable() {
        return ['name'];
    }

    public function defaultSorting() {
        return ['name' => 'ASC'];
    }
}
