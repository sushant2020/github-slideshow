<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\InternalSupplier;
use Faker\Generator as Faker;

$factory->define(InternalSupplier::class, function (Faker $faker) {
    return [
        'code' => $faker->word,
        'supplier_type' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});