<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\NewsFeed;
use Faker\Generator as Faker;

$factory->define(NewsFeed::class, function (Faker $faker) {
    return [
        'module_id' => $faker->randomNumber(),
        'name' => $faker->name,
        'description' => $faker->text,
        'is_active' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});