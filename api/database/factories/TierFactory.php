<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Tier;
use Faker\Generator as Faker;

$factory->define(Tier::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});