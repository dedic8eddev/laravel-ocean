<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Domain\Models\Organization::class, function (Faker $faker) {
    return [
        "name" => $faker->words(2, true),
        "domain" => $faker->words(1, true),
        "uuid" => $faker->words(1, true),
        "schema" => $faker->words(1, true),
        "active" => $faker->boolean,
    ];
});
