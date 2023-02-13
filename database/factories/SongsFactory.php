<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Songs;
use Faker\Generator as Faker;

$factory->define(Songs::class, function (Faker $faker) {
    $word1 = $faker -> word;
    $word2 = $faker -> word;
    return [
        'title' =>  $word1 . ' ' . $word2 ,
        'composer' => $faker -> name,
        'album' => $faker -> name,
        'uploaded_by' => $faker -> name,
        'lyrics' => $faker -> paragraph,
        'image_link' => $faker -> word,
    ];
});
