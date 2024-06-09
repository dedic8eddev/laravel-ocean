<?php

namespace App\Domain\Queries;

use App\Domain\Models\Organization;
use App\Http\Resources\OrganizationResource;
use WebThatMatters\DynamicFilter\Filters\BooleanFilter;
use WebThatMatters\DynamicFilter\Filters\CompositeFilter;
use WebThatMatters\DynamicFilter\Filters\StringFilter;

class OrganizationTableQuery extends TableQueryWithSearch {

    function baseQuery() {
        return Organization::query();
    }

    public function filters() {
        $this->filter('name', new StringFilter());
        $this->filter('domain', new StringFilter());
        $this->filter('active', new BooleanFilter());

        $this->filter('search', CompositeFilter::create([
            StringFilter::create(['name' => 'name']),
            StringFilter::create(['name' => 'domain']),
        ]));
    }

    public function sortable() {
        return ['name', 'domain'];
    }

    public function defaultSorting() {
        return ['name' => 'ASC'];
    }

    public function transform($data) {
        return OrganizationResource::collection($data);
    }
}
