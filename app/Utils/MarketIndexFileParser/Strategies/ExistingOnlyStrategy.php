<?php

namespace App\Utils\MarketIndexFileParser\Strategies;

use App\Models\MarketIndex;
use Illuminate\Support\Collection;

class ExistingOnlyStrategy implements MarketIndexMappingStrategy {
    public function getMarketIndexesIdMappings(Collection $names): Collection {
        return $storedMarketIndexes = MarketIndex::query()
                                                 ->whereIn('name', $names)
                                                 ->get(['id', 'name']);
    }
}