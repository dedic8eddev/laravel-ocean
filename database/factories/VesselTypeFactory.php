<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Domain\Models\VesselType::class, function (Faker $faker) {
    return [
        "name"     => $faker->word,
        "category" => $faker->word,
    ];
});