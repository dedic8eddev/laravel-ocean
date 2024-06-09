<?php

namespace App\Domain\Queries;

use App\Domain\Models\MarketIndex;
use App\Http\Resources\MarketIndexResource;
use WebThatMatters\DynamicFilter\DynamicFilter;
use WebThatMatters\DynamicFilter\Filters\StringFilter;
use WebThatMatters\DynamicFilter\Filters\NumberFilter;

class MarketIndexTableQuery extends DynamicFilter {

    function baseQuery() {
        return MarketIndex::query();
    }

    public function filters() {
        $this->filter('name', new StringFilter());
        $this->filter('issuer', new StringFilter());
        $this->filter('frequency', new StringFilter());
        $this->filter('vessel_size', new StringFilter());
        $this->filter('source', new StringFilter());
        $this->filter('vessel_type_id', new NumberFilter());
    }

    public function sortable() {
        return ['name', 'issuer', 'frequency', 'vessel_size', 'source', 'vessel_type_id'];
    }

    public function defaultSorting() {
        return ['vessel_type_id' => 'ASC', 'vessel_subtype_id' => 'ASC', 'name' => 'ASC'];
    }

    public function transform($data) {
        return MarketIndexResource::collection($data);
    }
}
