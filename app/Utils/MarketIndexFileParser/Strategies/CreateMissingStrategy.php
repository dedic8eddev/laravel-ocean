<?php

namespace App\Utils\MarketIndexFileParser\Strategies;

use App\Models\MarketIndex;
use Illuminate\Support\Collection;

class CreateMissingStrategy implements MarketIndexMappingStrategy {

    public function getMarketIndexesIdMappings(Collection $names): Collection {
        $storedMarketIndexes = MarketIndex::query()
                                          ->whereIn('name', $names)
                                          ->get(['id', 'name']);

        $marketIndexNames = $names->diff($storedMarketIndexes->pluck('name'));
        if ($marketIndexNames->count() > 0) {
            // insert new market indexes
            MarketIndex::insert($marketIndexNames->map(function ($name) {
                return ["name" => $name];
            })->all());
            // and then get their ids
            $newMarketIndexes = MarketIndex::query()->whereIn('name', $marketIndexNames)->get(['id', 'name']);

            $storedMarketIndexes = $storedMarketIndexes->concat($newMarketIndexes);
        }

        return $storedMarketIndexes;
    }
}