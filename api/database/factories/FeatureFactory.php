<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Feature;
use Faker\Generator as Faker;

$factory->define(Feature::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});