<?php

namespace App\Domain\Repositories;

use App\Domain\Models\MarketIndex;
use App\Domain\Models\MarketIndexValue;
use App\Domain\Queries\MarketIndexTableQuery;
use App\Domain\Queries\MarketIndexValueQuery;

class MarketIndexRepository extends BaseRepository {
    public function getMarketIndexById(int $id): MarketIndex {
    }

    /**
     * Get market indexes.
     *
     * @param array $params
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function table(array $params) {
        return MarketIndexTableQuery::create($params)->apply();
    }

    public function getMarketIndexValues(MarketIndex $index, array $params) {
        $query = (new MarketIndexValueQuery($index->values()))->apply($params);
        $this->sort($query, $params, ['value_date', 'value'], ['value_date']);
        return $this->paginate($query, $params);
    }

    public function getMarketIndexValuesById(int $id, array $params) {
        $query = (new MarketIndexValueQuery())->apply(array_merge($params, ["market_index_id" => $id]));
        return $this->paginate($query, $params);
    }

    public function storeMarketIndex(array $data) : MarketIndex {
        $marketIndex = new MarketIndex($data);
        $marketIndex->save();
        return $marketIndex;
    }

    public function updateMarketIndex(MarketIndex $index, array $data): MarketIndex {
        $index->fill($data);
        $index->save();
        return $index;
    }

    public function updateMarketIndexById(int $id, array $data) : bool {
        return MarketIndex::query()->where('id', $id)
                            ->update($data);
    }

    public function addValueToMarketIndex(int $id, array $data): MarketIndexValue {
        $marketIndexValue = new MarketIndexValue($data);
        $marketIndexValue->market_index_id = $id;
        $marketIndexValue->save();
        return $marketIndexValue;
    }

    public function bulkInsertValues(array $data) {
        MarketIndexValue::insert(array_map(function($datum) {
            $datum['created_at'] = \DB::raw('NOW()');
            $datum['updated_at'] = \DB::raw('NOW()');
            return $datum;
        }, $data));
    }
}