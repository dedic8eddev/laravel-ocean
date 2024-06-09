<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Domain\Models\Port::class, function (Faker $faker) {
    return [
        "name" => $faker->words(6, true),
        "code" => $faker->words(6, true),
        "country_code" => $faker->words(6, true),
    ];
});
