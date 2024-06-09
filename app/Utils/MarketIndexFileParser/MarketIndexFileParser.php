<?php

namespace App\Utils\MarketIndexFileParser;

use App\Utils\MarketIndexFileParser\ClarksonsCSVParser\CSVParser;
use App\Utils\MarketIndexFileParser\Strategies\ExistingOnlyStrategy;
use App\Utils\MarketIndexFileParser\Strategies\MarketIndexMappingStrategy;
use Illuminate\Support\Collection;
use League\Csv\Reader;

class MarketIndexFileParser {
    public static function forFile($file, MarketIndexMappingStrategy $strategy = null) {
        if ($strategy === null)
            $strategy = new ExistingOnlyStrategy();

        return new static($file, $strategy);
    }

    /**
     * @var \SplFileInfo
     */
    private $file;
    /**
     * @var MarketIndexMappingStrategy
     */
    private $strategy;

    private function __construct($file, $strategy) {
        $this->file = $file;
        $this->strategy = $strategy;
    }

    public function chunk($chunkSize, $callback) {
        /** @var \League\Csv\Reader $reader */
        $reader = Reader::createFromPath($this->file->getRealPath());
        $records = $reader->getRecords();


        CSVParser::from($records)->getDataChunked($chunkSize, function(array $data) use ($callback) {
            $nameIdMap = $this->strategy->getMarketIndexesIdMappings(collect($data)->pluck('name'))->keyBy('name');

            $marketIndexes = collect();
            foreach ($data as $datum) {
                $mapping = $nameIdMap->get($datum['name']);
                if ($mapping != null) {
                    foreach ($datum['values'] as $value) {
                        $marketIndexes->push([
                            "value"           => MarketIndexFileParser::toNumber($value['value']),
                            "value_date"      => $value['value_date'],
                            "market_index_id" => $mapping->id
                        ]);
                    }
                }
            }

            $callback($marketIndexes);
        });
    }

    protected static function toNumber(string $str) {
        return str_replace(",", "", $str);
    }
}