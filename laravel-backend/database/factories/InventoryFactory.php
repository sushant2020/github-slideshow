<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\Inventory;
use Faker\Generator as Faker;

$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'Company_Id' => $faker->randomNumber(),
        'Product_Id' => $faker->randomNumber(),
        'Depot_Id' => $faker->randomNumber(),
        'LG_Date' => $faker->date(),
        'LS_Date' => $faker->date(),
        'Physical_Stock' => $faker->randomNumber(),
        'Allocation_Stock' => $faker->randomNumber(),
        'Allocation_After' => $faker->randomNumber(),
        'On_Order' => $faker->randomNumber(),
        'Backorder' => $faker->randomNumber(),
        'LG_Number' => $faker->e164PhoneNumber,
        'LPP_Cost' => null,
        'Avg_Cost' => null,
        'TCost' => null,
        'Min_Stock' => $faker->randomNumber(),
        'Std_Cost' => null,
        'Max_Stock' => null,
        'Average_usage' => $faker->randomNumber(),
        'Pick_Bin' => $faker->word,
        'Average_usage_UOM' => $faker->word,
        'Average_usage_Period' => $faker->word,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});