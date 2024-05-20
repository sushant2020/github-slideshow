<?php

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */

use App\Models\GRN;
use Faker\Generator as Faker;

$factory->define(GRN::class, function (Faker $faker) {
    return [
        'Company_Id' => $faker->randomNumber(),
        'Supplier_Id' => $faker->randomNumber(),
        'Product_Id' => $faker->randomNumber(),
        'Depot_Id' => $faker->randomNumber(),
        'Foreign_Currency_Id' => $faker->randomNumber(),
        'PurchaseOrder_Id' => $faker->randomNumber(),
        'PurchaseOrder_Operator_Id' => $faker->randomNumber(),
        'Grn_No' => $faker->randomNumber(),
        'Grn_Qty' => $faker->randomNumber(),
        'Grn_Value' => null,
        'Grn_Price' => null,
        'GRN_Exchange_Rate' => null,
        'Purchase_Order_No' => $faker->randomNumber(),
        'Purchase_Order_Line_No' => $faker->randomNumber(),
        'Purchase_Order_Line_Desc' => $faker->word,
        'PurchaseOrder_Value' => null,
        'PurchaseOrder_Qty' => $faker->randomNumber(),
        'PurchaseOrder_FC_Value' => null,
        'PurchaseOrder_Exchange_Rate' => null,
        'PurchaseOrder_Type' => $faker->word,
        'Master_Order_No' => $faker->word,
        'Sales_Order_No' => $faker->word,
        'Sales_Order_Line_No' => $faker->word,
        'Sell_By_Date' => $faker->date(),
        'Due_Date' => $faker->date(),
        'Order_Date' => $faker->date(),
        'Receipt_Date' => $faker->date(),
        'Qty_Desc' => $faker->word,
        'Price_Desc' => $faker->word,
        'Weight' => null,
        'Period' => $faker->word,
        'Days_Late' => $faker->randomNumber(),
        'Late_Qty' => $faker->randomNumber(),
        'Return_Qty' => $faker->randomNumber(),
        'Claim_Qty' => $faker->randomNumber(),
        'Trans_Anal_6' => $faker->word,
        'Account_Year' => $faker->userName,
        'Account_Month' => $faker->userName,
        'Cost_in_Currency' => null,
        'created_at' => 'CURRENT_TIMESTAMP',
        'updated_at' => $faker->datetime(),
        'inserted_by' => $faker->randomNumber(),
        'lastchanged_by' => $faker->randomNumber(),
    ];
});