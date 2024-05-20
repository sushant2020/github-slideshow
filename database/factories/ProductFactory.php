<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'product_code' => $faker->word,
        'sm_description' => $faker->text,
        'sm_description2' => $faker->text,
        'sm_description3' => $faker->text,
        'sm_analysis_code1' => $faker->word,
        'sm_analysis_code2' => $faker->word,
        'sm_analysis_code3' => $faker->word,
        'ac4' => $faker->word,
        'sm_analysis_code5' => $faker->word,
        'sm_analysis_code6' => $faker->word,
        'product_group' => $faker->word,
        'product_group2' => $faker->word,
        'sm_bin_loc' => $faker->word,
        'ske_analysis26' => $faker->word,
        'vmpp_char_count' => $faker->randomNumber(),
        'dt_description' => $faker->word,
        'dt_pack' => $faker->randomNumber(),
        'dt_type' => $faker->word,
        'dt_price' => $faker->randomFloat(),
        'pref_supplier' => $faker->word,
        'vat_code' => $faker->randomNumber(),
        'status' => $faker->word,
        'sedes_pip_code' => $faker->randomNumber(),
        'temperature' => $faker->word,
        'list_price' => $faker->randomFloat(),
        'desc_pack' => $faker->text,
        'spec_disc' => $faker->boolean,
        'own_brand' => $faker->word,
        'cust_brand' => $faker->word,
        'additional1' => $faker->word,
        'additional2' => $faker->randomFloat(),
        'additional3' => $faker->word,
        'last_purchase_cost' => $faker->randomFloat(),
        'standard_cost' => $faker->randomFloat(),
        'case_description' => $faker->word,
        'minimum_shelf_life' => $faker->boolean,
        'company' => $faker->boolean,
        'dq_status' => $faker->word,
        'product_type' => $faker->boolean,
        'final_price' => $faker->randomFloat(),
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});