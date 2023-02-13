<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Playlist;
use Faker\Generator as Faker;

$factory->define(Playlist::class, function (Faker $faker) {
    return [
        'thumb_link' => $faker -> word,
        'title' => $faker -> word,
        'sub_title' => $faker -> word,
        'description' => $faker -> word,
        'banner_link' => $faker -> word,
    ];
});
