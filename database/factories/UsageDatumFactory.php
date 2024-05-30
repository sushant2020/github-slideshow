<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Source;
use App\Models\Supplier;
use App\Models\Tier;
use App\Models\UsageDatum;
use Faker\Generator as Faker;

$factory->define(UsageDatum::class, function (Faker $faker) {
    return [
        'tier_id' => function () {
            return factory(Tier::class)->create()->id;
        },
        'source_id' => function () {
            return factory(Source::class)->create()->id;
        },
        'parent_product_code' => $faker->word,
        'supplier_id' => function () {
            return factory(Supplier::class)->create()->id;
        },
        'internal_supplier_id' => $faker->randomNumber(),
        'logger_id' => $faker->randomNumber(),
        'volume' => $faker->randomFloat(),
        'volume_from_date' => $faker->date(),
        'volume_untill_date' => $faker->date(),
        'original_volume_from_date' => $faker->date(),
        'original_volume_untill_date' => $faker->date(),
        'comment' => $faker->text,
        'is_deleted' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});