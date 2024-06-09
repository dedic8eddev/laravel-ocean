<?php

namespace App\Domain\Queries;

use Illuminate\Support\Arr;
use WebThatMatters\DynamicFilter\DynamicFilter;

abstract class TableQueryWithSearch extends DynamicFilter {

    public function __construct($config) {
        $filters = Arr::get($config, 'filters', []);
        $search = Arr::pull($config, 'search');
        if($search){
            $config['filters'] = array_merge([['name' => 'search', 'operation' => 'contains', 'value' => $search]], $filters);
        }
        parent::__construct($config);
    }
}
