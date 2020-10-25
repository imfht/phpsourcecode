<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Todo::class, function (Faker $faker) {
    $beginAt = Carbon::now();
    $endAt = Carbon::now()->addDay($faker->randomDigit);

    return [
        'content' => $faker->text,
        'status' => $faker->numberBetween(0, 2),
        'importance' => $faker->numberBetween(0, 3),
        'begin_at' => $beginAt,
        'end_at' => $endAt,
    ];
});
