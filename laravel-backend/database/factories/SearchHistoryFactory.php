<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\SearchHistory;
use Faker\Generator as Faker;

$factory->define(SearchHistory::class, function (Faker $faker) {
    return [
        'module' => $faker->randomNumber(),
        'keyword' => $faker->word,
        'created_at' => 'CURRENT_TIMESTAMP',
        'inserted_by' => $faker->randomNumber(),
    ];
});