<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Domain\Models\Country::class, function (Faker $faker) {
    return [
        "code" => $faker->word,
        "name" => $faker->word,
    ];
});
