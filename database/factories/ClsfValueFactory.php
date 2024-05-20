<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\ClsfDefn;
use App\Models\ClsfValue;
use Faker\Generator as Faker;

$factory->define(ClsfValue::class, function (Faker $faker) {
    return [
        'clsf_defn_id' => function () {
            return factory(ClsfDefn::class)->create()->id;
        },
        'value' => $faker->word,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});