<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'module_id' => $faker->randomNumber(),
        'product_id' => $faker->randomNumber(),
        'supplier_id' => $faker->randomNumber(),
        'name' => $faker->name,
        'assigned_to' => $faker->randomNumber(),
        'status' => $faker->boolean,
        'priority' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});