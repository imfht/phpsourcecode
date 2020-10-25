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
    ];
});

$factory->define(App\TaskBank::class, function (Faker\Generator $faker) {
    return [
        'title' => str_random(20),
        'target' => str_random(20),
        'published' => true,
        'startTime' => $faker->dateTime,
        'deadLine' => '2018-03-25 06:28:32',
    ];
});

$factory->define(App\QuestionBank::class, function (Faker\Generator $faker) {
    return [
        'content' => json_encode(array("question" => str_random(30),
                                        "options" => array(str_random(20),str_random(20),str_random(20),str_random(20)))),
        'answers' => rand(0,3)
    ];
});

$factory->define(App\TaskReport::class, function (Faker\Generator $faker) {
    return [
        'content' => json_encode(array("1"=>1,"2"=>1,"3"=>1,"4"=>1,"5"=>1)),
        'taskId' => 1
    ];
});


