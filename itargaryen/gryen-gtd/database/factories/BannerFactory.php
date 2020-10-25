<?php

use Faker\Generator as Faker;

$factory->define(App\Banner::class, function (Faker $faker) {
    return [
        'cover' => env('SITE_DEFAULT_IMAGE'),
        'status' => 1,
    ];
});
