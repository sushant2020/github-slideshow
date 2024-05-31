<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\ClsfValue;
use App\Models\ProductClassification;
use Faker\Generator as Faker;

$factory->define(ProductClassification::class, function (Faker $faker) {
    return [
        'clsf_value_id' => function () {
            return factory(ClsfValue::class)->create()->id;
        },
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});