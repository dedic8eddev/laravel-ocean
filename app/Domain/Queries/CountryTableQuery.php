<?php

namespace App\Domain\Queries;

use App\Domain\Models\Country;
use App\Http\Resources\CountryResource;
use WebThatMatters\DynamicFilter\DynamicFilter;
use WebThatMatters\DynamicFilter\Filters\StringFilter;

class CountryTableQuery extends DynamicFilter {

    function baseQuery() {
        return Country::query();
    }

    public function filters() {
        $this->filter('name', new StringFilter());
    }

    public function sortable() {
        return ['name'];
    }

    public function defaultSorting() {
        return ['name' => 'ASC'];
    }

    public function transform($data) {
        return CountryResource::collection($data);
    }
}
