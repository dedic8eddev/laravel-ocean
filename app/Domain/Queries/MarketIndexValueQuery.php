<?php

namespace App\Domain\Queries;

use App\Domain\Models\MarketIndexValue;
use App\Traits\Queries\FiltersQueries;
use Illuminate\Database\Eloquent\Builder;

class MarketIndexValueQuery {

    use FiltersQueries;
    private $query = null;

    public function __construct($query = null) {
        $this->registerCallback("from", function ($query, $value) {
            $query->where('value_date', '>=', $value);
        });

        $this->registerCallback("to", function ($query, $value) {
            $query->where('value_date', "<=", $value);
        });

        $this->query = $query;
    }

    public function baseQuery() {
        if (!is_null($this->query)) {
            return $this->query;
        }

        return MarketIndexValue::query();
    }

    /**
     * @param $params
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function apply($params) {
        return $this->filter($this->baseQuery(), $params, ['from', 'to', 'market_index_id']);
    }
}