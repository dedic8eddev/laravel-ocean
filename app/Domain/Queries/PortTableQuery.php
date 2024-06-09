<?php

namespace App\Domain\Queries;

use App\Domain\Models\Port;
use App\Http\Resources\PortResource;
use WebThatMatters\DynamicFilter\DynamicFilter;
use WebThatMatters\DynamicFilter\Filters\StringFilter;

class PortTableQuery extends DynamicFilter {

    function baseQuery() {
        return Port::query();
    }

    public function filters() {
        $this->filter('name', new StringFilter());
        $this->filter('code', new StringFilter());
        $this->filter('country_code', new StringFilter());
        $this->filter('type', new StringFilter());
        $this->filter('size', new StringFilter());
    }

    public function sortable() {
        return ['name', 'code', 'country_code', 'type', 'size'];
    }

    public function defaultSorting() {
        return ['name' => 'ASC'];
    }

    public function transform($data) {
        return PortResource::collection($data);
    }
}
