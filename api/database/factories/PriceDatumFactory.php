<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\ImportLogger;
use App\Models\PriceDatum;
use App\Models\Source;
use App\Models\Supplier;
use App\Models\Tier;
use Faker\Generator as Faker;

$factory->define(PriceDatum::class, function (Faker $faker) {
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
        'logger_id' => function () {
            return factory(ImportLogger::class)->create()->id;
        },
        'forecast' => $faker->randomFloat(),
        'price' => $faker->randomFloat(),
        'price_from_date' => $faker->date(),
        'price_untill_date' => $faker->date(),
        'original_price_from_date' => $faker->date(),
        'original_price_untill_date' => $faker->date(),
        'notes' => $faker->text,
        'comment' => $faker->text,
        'is_deleted' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});