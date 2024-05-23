<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Faker\Generator as Faker;

$factory->define(PurchaseOrder::class, function (Faker $faker) {
    return [
        'product_id' => function () {
            return factory(Product::class)->create()->id;
        },
        'supplier_id' => function () {
            return factory(Supplier::class)->create()->id;
        },
        'suggested_quantity' => $faker->randomNumber(),
        'preferred_price' => $faker->randomFloat(),
        'notes' => $faker->text,
        'status' => $faker->boolean,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'created_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
        'verified_by' => $faker->randomNumber(),
        'verified_at' => $faker->datetime(),
    ];
});