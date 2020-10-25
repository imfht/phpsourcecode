<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Biji::class, function (Faker\Generator $faker) {
    return [
        'user_id'=>rand(1,3),
        'book_id' =>rand(1,3),
        'title' => $faker->sentence(mt_rand(3, 10)),
        'content' => join("\n\n", $faker->paragraphs(mt_rand(3, 6))),
        'published_at' => $faker->dateTimeBetween('-1 month', '+3 days'),
    ];
});

$factory->define(App\Book::class, function (Faker\Generator $faker) {
    return [
        'user_id'=>rand(1,3),
        'title' => $faker->sentence(mt_rand(3, 10)),
    ];
});
