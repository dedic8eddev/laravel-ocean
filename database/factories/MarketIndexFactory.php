<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Domain\Models\MarketIndex::class, function (Faker $faker) {
    return [
        "name"           => $faker->words(6, true),
        "issuer"         => $faker->words(2, true),
        "frequency"      => $faker->randomElement(["daily", "monthly", "annually"]),
        "source"         => $faker->company,
        "value_unit"     => $faker->randomElement(['$/day', '$/week', '$/month']),
        "vessel_type_id" => function () {
            return factory(App\Domain\Models\VesselType::class)->create()->id;
        },
    ];
});

$factory->define(App\Domain\Models\MarketIndexValue::class, function (Faker $faker) {
    return [
        "value"           => $faker->numberBetween(1000, 30000),
        "value_date"      => $faker->date(),
        "market_index_id" => function () {
            return factory(App\Domain\Models\MarketIndex::class)->create()->id;
        },
    ];
});