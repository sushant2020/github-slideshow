<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\components\Helper;
use App\Models\DwProduct;
use App\Models\ClDesProduct;
use App\Models\Product;

class ProductCombo extends Model
{

    /**
     * The table associated with the model.
     * This table stores the product data  fetching from csv file provided by Krishna.
     * This table has clean description for some products
     *
     * @var string
     */
    protected $table = 'products_combo';
    protected $primaryKey = 'prod_id';
    protected $fillable = [
        'product_code',
        'sm_analysis_code1',
        'sm_analysis_code2',
        'sm_analysis_code3',
        'sm_analysis_code4',
        'sm_analysis_code5',
        'sm_analysis_code6',
        'dt_description',
        'dt_type',
        'dt_pack',
        'sm_description',
        'sm_description2',
        'sm_description3',
        'product_group',
        'product_group2',
        'sm_bin_loc',
        'ske_analysis26',
        'vmpp_char_count',
        'vmpp',
        'ampp',
        'vmpp_description',
        'ampp_description',
        'clean_description',
        'dt_price',
        'final_price'
    ];
    public $sortable = ['ac4', 'product_code', 'status'];

    /**
     * Imports the product data into cl_des_products table from file provided By Sigma/Krishna.
     * This file has clean description for products
     * 
     * @return integer Existing Product Count
     */
    public function updateDescription()
    {
        ini_set('max_execution_time', '800');
        $desc_status = $temp_ac4 = "";
        $newProductData = $data = [];
        $userId = 1;

        //Get all Datawarehouse products

        $dwProducts = Product::where("is_parent", 1)->where('clean_description', 'LIKE', '-%')->get()->toArray();
	

        try {
            foreach ($dwProducts as $dwProductItem) {

                $product_code = !empty($dwProductItem) && isset($dwProductItem["product_code"]) ? trim($dwProductItem["product_code"]) : "";

                $parentProductCode = !empty($dwProductItem) && isset($dwProductItem["ac4"]) ? trim($dwProductItem["ac4"]) : "";
                 $product_desc = !empty($dwProductItem) && !empty($dwProductItem["product_desc"]) && isset($dwProductItem["product_desc"]) ? trim($dwProductItem["product_desc"]) : "";

                if (!empty($product_code) || !empty($parentProductCode)) {
                    $pricingProductItem = ClDesProduct::where(["sm_analysis_code4" => $parentProductCode])->first();
                
                    $clean_description = !empty($pricingProductItem) && !empty($pricingProductItem["clean_description"]) && isset($pricingProductItem["clean_description"]) ? trim($pricingProductItem["clean_description"]) : "";
                   
                }

                $spotDesc = DB::table('DwProduct')
                                ->where('Product_AC_4', '=', $parentProductCode)
                                ->where('Is_Spot', '=', 1)->where('Product_Desc', 'NOT LIKE', '-%')
                                ->pluck('Product_Desc')->first();

                $dwDesc = DB::table('DwProduct')
                                ->where('Product_AC_4', '=', $parentProductCode)
                                ->where('Product_AC_5', '=', 'SPOT')
                                ->where('Product_Status', '=', 'Live')
                                ->where('Product_Desc', 'NOT LIKE', '-%')
                                ->pluck('Product_Desc')->first();

                $dwDescO = DB::table('DwProduct')
                                ->where('Product_AC_4', '=', $parentProductCode)
                                ->where('Product_Status', '=', 'Live')
                                ->where('Product_Desc', 'NOT LIKE', '-%')
                                ->pluck('Product_Desc')->first();

                if (!empty($clean_description)) {
                    $description = $clean_description;
                } else if (!empty($spotDesc)) {
                    $description = $spotDesc;
                } else if (!empty($dwDesc)) {
                    $description = $dwDesc;
                }  else if (!empty($dwDescO)) {
                    $description = $dwDescO;
                } else {
                    $description = $product_desc;
                }
                
                $existingProductId = $dwProductItem['prod_id'];
                
                $data = array(
                    "clean_description" => $description,
                    "inserted_by" => $userId,
                    "lastchanged_by" => $userId,
                    "updated_at" => now());

                if (!empty($existingProductId)) {
                    DB::table('products')
                            ->where(["prod_id" => $existingProductId])
                            ->update($data);
                }
            }
        } catch (\Exception $error) {
            echo $error->getMessage();
        }
    }

}
