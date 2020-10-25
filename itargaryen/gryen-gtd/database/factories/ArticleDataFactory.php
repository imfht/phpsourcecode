<?php

use Faker\Generator as Faker;

$factory->define(App\ArticleData::class, function (Faker $faker) {
    return [
        'content' => $faker->text,
    ];
});
