<?php

namespace Tests\Feature;

use App\Domain\Models\MarketIndex;
use App\Domain\Models\MarketIndexValue;
use App\Domain\Models\VesselType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarketIndexValuesTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function testMarketIndexBulkInsert() {
        $id = factory(MarketIndex::class)->create()->id;
        $id1 = factory(MarketIndex::class)->create()->id;

        $request = [
            "date" => Carbon::now()->toDateTimeString(),
            "index" => [
                [
                    "market_index_id" => $id,
                    "value" => $this->faker->numberBetween(1000, 30000),
                ],
                [
                    "market_index_id" => $id1,
                    "value" => $this->faker->numberBetween(1000, 30000),
                ]
            ]
        ];

        $res = $this->post("/market-indexes/$id/values", $request);
        $res->assertStatus(200)
            ->assertJsonFragment(["inserted" => count($request["index"])]);

        $this->assertDatabaseHas((new MarketIndexValue())->getTable(), [
            "value" => $request['index'][0]['value'],
            "market_index_id" => $request['index'][0]['market_index_id']
        ]);
    }

    public function testMarketIndexInsert() {
        $request = [
            "name" => $this->faker->words(5, true),
            "issuer" => $this->faker->company,
            "frequency" => $this->faker->randomElement(['monthly', 'daily']),
            "vessel_type_id" => factory(VesselType::class)->create()->id,
            "vessel_size" => $this->faker->word,
            "source" => $this->faker->company,
            "value_unit" => $this->faker->word
        ];

        $res = $this->post("/market-indexes", $request);
        $res->assertStatus(201)
            ->assertJsonStructure(
                ['data' => ['id']]
            );

        $this->assertDatabaseHas((new MarketIndex())->getTable(), array_merge(
            $request, ["id" => $res->json()["data"]['id']]
        ));
    }

    public function testMarketIndexUpdate() {
        $id = factory(MarketIndex::class)->create()->id;

        $request = [
            "name" => $this->faker->words(3, true),
            "issuer" => $this->faker->company
        ];

        $res = $this->put("/market-indexes/$id", $request);
        $res->assertStatus(200);
        $this->assertDatabaseHas(
          (new MarketIndex())->getTable(),
          array_merge($request, ["id" => $id])
        );
    }

    public function testMarketIndexValueListRange() {
        $now = Carbon::parse("2019-01-01");
        $from = $now->copy()->subDays(2);
        $to = $now->copy()->addDays(2);
        $out1 = $from->copy()->subDays(1);
        $out2 = $to->copy()->addDays(1);

        $index = factory(MarketIndex::class)->create();

        $mapDateToIndexes = function($date) use ($index) {
            return factory(MarketIndexValue::class)->create([
                'value_date' => $date,
                'market_index_id' => $index->id
            ]);
        };

        $valid = array_map($mapDateToIndexes, [$now, $from, $to]);
        $invalid = array_map($mapDateToIndexes, [$out1, $out2]);

        $res = $this->get("/market-indexes/$index->id/values?" . http_build_query([
            "from" => $from->toDateTimeString(),
            "to" => $to->toDateTimeString()
        ]));
        $res->assertStatus(200);

        foreach ($valid as $value)
            $res->assertJsonFragment([ "id" => $value->id ]);

        foreach ($invalid as $value)
            $res->assertJsonMissing(["id" => $value->id]);

    }
}
