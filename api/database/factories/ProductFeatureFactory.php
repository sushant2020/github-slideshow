<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Feature;
use App\Models\ProductFeature;
use Faker\Generator as Faker;

$factory->define(ProductFeature::class, function (Faker $faker) {
    return [
        'feature_id' => function () {
            return factory(Feature::class)->create()->id;
        },
        'feature_value' => $faker->word,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});