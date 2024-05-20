<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Supplier;
use Faker\Generator as Faker;

$factory->define(Supplier::class, function (Faker $faker) {
    return [
        'company_id' => $faker->randomNumber(),
        'currency_id' => $faker->randomNumber(),
        'code' => $faker->word,
        'name' => $faker->name,
        'add1' => $faker->word,
        'add2' => $faker->word,
        'add3' => $faker->word,
        'postcode' => $faker->word,
        'contact' => $faker->word,
        'telNo' => $faker->word,
        'email' => $faker->safeEmail,
        'buyer_code' => $faker->word,
        'group_code' => $faker->word,
        'category_code' => $faker->word,
        'contact_telNo' => $faker->word,
        'contact_mobile' => $faker->word,
        'contact_email' => $faker->safeEmail,
        'stop_ind' => $faker->word,
        'supplier_type' => $faker->boolean,
        'lastPaid_date' => $faker->datetime(),
        'lastPaid_amount' => null,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});