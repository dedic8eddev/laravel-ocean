<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Domain\Models\Cargo::class, function (Faker $faker) {
    return [
        "name"        => $faker->words(6, true),
        "description" => $faker->words(10, true),
    ];
});
