<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Accounts;
use Faker\Generator as Faker;

$factory->define(Accounts::class, function (Faker $faker) {
    return [
        //
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
        'email' => $faker->email,
        'password' => $faker->password
    ];
});
