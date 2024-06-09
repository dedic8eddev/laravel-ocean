<?php

namespace App\Http\Controllers;

use App\Domain\Models\MarketIndex;
use App\Domain\Repositories\MarketIndexRepository;
use App\Http\Requests\BulkAddValuesRequest;
use App\Http\Requests\MarketIndexRequest;
use App\Http\Resources\MarketIndexResource;
use App\Http\Resources\MarketIndexValueResource;
use App\Utils\MarketIndexFileParser\MarketIndexFileParser;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MarketIndexController extends Controller {

    private $repo;

    public function __construct(MarketIndexRepository $repo) {
        $this->repo = $repo;
    }

    public function index(Request $request) {
        $validationRules = [
            "name"           => "nullable|string",
            "issuer"         => "nullable|string",
            "frequency"      => "nullable|string",
            "vessel_size"    => "nullable|string",
            "source"         => "nullable|string",
            "vessel_type_id" => "nullable|integer|exists:vessel_types,id",
        ];

        $this->validate($request, $validationRules);

        $marketIndexes = $this->repo->table($request->all());

        return MarketIndexResource::collection($marketIndexes);
    }

    public function store(MarketIndexRequest $request) {
        $marketIndex = $this->repo->storeMarketIndex($request->all());
        return new MarketIndexResource($marketIndex);
    }

    public function update(MarketIndex $index, MarketIndexRequest $request) {
        $index = $this->repo->updateMarketIndex($index, $request->all());
        return new MarketIndexResource($index);
    }

    public function indexValues(MarketIndex $index, Request $request) {
        $validationRules = [
            "from"         => "nullable|date",
            "to"           => "nullable|date",
            "per_page"     => "integer",
            "current_page" => "integer",
        ];

        $this->validate($request, $validationRules);

        $marketIndexValues = $this->repo->getMarketIndexValues(
            $index,
            $request->only(array_keys($validationRules))
        );

        return MarketIndexValueResource::collection($marketIndexValues);
    }

    public function bulkIndexValues(Request $request) {
        $this->validate($request, [
            "indexes"      => "required|array|exists:market_indexes,id",
            "indexes.*"    => "required|integer",
            "from"         => "nullable|date",
            "to"           => "nullable|date",
            "per_page"     => "integer",
            "current_page" => "integer",
        ]);

        $result = [];
        // count($indexes) queries :(
        foreach ($request->input('indexes') as $index) {
            $result[$index] = $this->repo->getMarketIndexValuesById($index, $request->only('from', 'to', 'per_page', 'current_page'));
        }

        return $result;
    }

    public function storeValues(BulkAddValuesRequest $request) {
        $requestValues = $request->only('date', 'index');

        $date = $requestValues['date'];
        $params = array_map(function ($index) use ($date) {
            return array_merge(
                Arr::only($index, ['value', 'market_index_id']),
                ['value_date' => $date]
            );
        }, $requestValues['index']);

        $this->repo->bulkInsertValues($params);

        return ["inserted" => count($params)];
    }


    public function uploadMarketIndexesFile(Request $request) {
        $this->validate($request, [
            "file" => "required|file",
        ]);

        $count = 0;
        \DB::transaction(function () use ($request, &$count) {
            MarketIndexFileParser::forFile($request->file('file'))
                                 ->chunk(100, function ($indexes) use (&$count) {
                                     $this->repo->bulkInsertValues($indexes->all());
                                     $count += count($indexes);
                                 });
        });

        return ["inserted" => $count];
    }
}
