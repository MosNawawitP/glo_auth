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
        'idcard'     => $faker->randomNumber,
        'password'   => password_hash('12345', PASSWORD_BCRYPT),
        'ipaddress'  => $faker->ipv4,
    ];
});
