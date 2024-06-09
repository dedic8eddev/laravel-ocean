<?php

namespace App\Utils\MarketIndexFileParser\Strategies;

use Illuminate\Support\Collection;

interface MarketIndexMappingStrategy {
    public function getMarketIndexesIdMappings(Collection $names): Collection;
}