<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Activity;
use Faker\Generator as Faker;

$factory->define(Activity::class, function (Faker $faker) {
    return [
        'module_id' => $faker->randomNumber(),
        'name' => $faker->name,
        'description' => $faker->text,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});