<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DwProduct;
use App\Models\Product;
use App\Models\ProductTags;
use Illuminate\Support\Facades\DB;
use App\Models\ClDesProduct;
use App\Models\PricingUsageData;
use App\Models\Inventory;
use App\Models\DwInventory;
use App\Models\GRN;
use App\Models\DwGRN;
use App\Models\PricingSupplierPriceData;
use App\Models\PricingSource;
use App\Models\Supplier;
use App\Models\Tag;
use App\Models\DwDepot;
use App\Models\Ghost;
use Auth;
use App\Components\Helper;

class Product extends Model
{

    protected $table = 'products';
    protected $primaryKey = 'prod_id';

    //Product statues
    public const INCOMPLETE = 0; # When Product is created manually
    public const COMPLETE = 1; # When product is created with initial migration or later updated with migration
    public const ACTIVE = 2;
    public const INACTIVE = 3;
    //Product Description Flags
    public const PRODUCT_DESC_DW = 1;
    public const PRODUCT_DESC_CLEAN = 2;
    public const PRODUCT_IS_PARENT_YES = 1;
    public const PRODUCT_IS_PARENT_NO = 0;

 
    /*
     * comments table cgroup 1 means buyer comment, cgroup 2 means pricer comment, cgroup 3 means task
     * 
     * 
     * type => 1 "Buyer Intel"
      type => 2 "PRESET FOR OFFICE-DAILY"
      type => 3 "RDS"
      type => 4 "Buyer Watchlist"
      type => 5 "Offers"
      type => 6 "PRESET FOR OFFICE - TWICE A WEEK"
     * type => 7 "Undercostlines"

     */

    protected $fillable = ['prod_id', 'ac4', 'company_id', 'product_code', 'dt_description', 'dt_price', 'ac1', 'ac2', 'onboarding_as', 'prod_status', 'inserted_by'];

//    protected $fillable = [
//        'product_code',
//        'sm_analysis_code1',
//        'sm_analysis_code2',
//        'sm_analysis_code3',
//        'ac4',
//        'sm_analysis_code5',
//        'sm_analysis_code6',
//        'dt_description',
//        'dt_type',
//        'dt_pack',
//        'sm_description',
//        'sm_description2',
//        'sm_description3',
//        'product_group',
//        'product_group2',
//        'sm_bin_loc',
//        'ske_analysis26',
//        'vmpp_char_count',
//        'vmpp',
//        'ampp',
//        'vmpp_description',
//        'ampp_description',
//        'clean_description',
//        'dt_price',
//        'final_price',
//    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }

    /**
     * Creates parent and child products by using product data from pricing and Dataware-house product table
     *
     * @return void
     */
    public function mapProductData($pricingProducts)
    {

        //Get all products from Datawarehouse
        $flag = 0;
        //All products with no parent considered as parent products
        $dwProductsA = DwProduct::where("Product_AC_4", "=", "")->orWhere("Product_AC_4", 'like', '%**%')->groupBy("Product_Code")->pluck("Product_Code")->toArray();

        $dwProductsA = array_unique($dwProductsA);

        if (!empty($dwProductsA)) {
            $productType = self::PRODUCT_IS_PARENT_YES;
            //No AC4 is NULL OR  blank OR AC4 is like "**"
            $flag = 1;
            self::storeParentProducts($dwProductsA, $productType, $flag);
        }

        mail('sushant@webdezign.co.uk', 'Product Mapping', 'Done - All products with no parent considered as parent products');
        //All products having child considered as parent products
        $dwProductsP = DwProduct::where("Product_AC_4", "<>", "")->where("Product_AC_4", 'not like', '%**%')->groupBy("Product_AC_4")->pluck("Product_AC_4")->toArray();

        if (!empty($dwProductsP)) {
            $productType = self::PRODUCT_IS_PARENT_YES;
            $flag = 0;
            self::storeParentProducts($dwProductsP, $productType, $flag);
        }
        mail('sushant@webdezign.co.uk', 'Product Mapping', 'Done - All products having child considered as parent products');

        //All products with no child code considered as parent products
        $dwProductsPP = DwProduct::where("Product_Code", "=", "")->orWhere("Product_Code", 'like', '%**%')->select("Product_Code", "Product_AC_4")->get()->toArray();
        if (!empty($dwProductsPP)) {
            $productType = self::PRODUCT_IS_PARENT_YES;
            $flag = 0;
            self::storeParentProducts($dwProductsPP, $productType, $flag);
        }
        mail('sushant@webdezign.co.uk', 'Product Mapping', 'Done - All products with no child code considered as parent products');

        //All products having parent considered as child products
        $dwProducts = DwProduct::where("Product_AC_4", "<>", "")->where("Product_Code", "<>", "")->where("Product_AC_4", 'not like', '%**%')->where("Product_Code", 'not like', '%**%')->get()->toArray();

        //Inserts child products into product portal table
        if (!empty($dwProducts)) {
            $productType = self::PRODUCT_IS_PARENT_NO;
            self::storeChildProducts($dwProducts, $productType);
        }

        mail('sushant@webdezign.co.uk', 'Product Mapping', 'Done - All products having parent considered as child products');
    }

    private static function storeParentProducts($dwProducts, $productType = NULL, $flag = 0)
    {
        $desc_status = $temp_ac4 = "";
        $newChildData = $data = [];
        $userId = 1;

        foreach ($dwProducts as $productItem) {

            if ($flag == 1) {
                $dwProductItem = DwProduct::where(["Product_Code" => $productItem])->first();
                $temp_ac4 = $productItem;
            } else {
                $dwProductItem = DwProduct::where(["Product_AC_4" => $productItem])->first();
            }
            $product_code = !empty($dwProductItem) && isset($dwProductItem["Product_Code"]) ? trim($dwProductItem["Product_Code"]) : "";
            $parentProductCode = !empty($dwProductItem) && isset($dwProductItem["Product_AC_4"]) ? trim($dwProductItem["Product_AC_4"]) : "";
            if (!empty($product_code) || !empty($parentProductCode)) {
                $pricingProductItem = ClDesProduct::where(["sm_analysis_code4" => $parentProductCode])->first();

                $clean_description = !empty($pricingProductItem) && isset($pricingProductItem["clean_description"]) ? trim($pricingProductItem["clean_description"]) : "";
                $dwProductDesc = !empty($dwProductItem["Product_Desc"]) && isset($dwProductItem["Product_Desc"]) ? trim($dwProductItem["Product_Desc"]) : "";
                $companyId = !empty($dwProductItem["Company_Id"]) && $dwProductItem["Company_Id"] ? trim($dwProductItem["Company_Id"]) : "";

                if ($flag == 1) {
                    $existingChildProduct = Product::where(["company_id" => $companyId, "product_code" => $product_code, "ac4" => $parentProductCode, "is_parent" => $productType])->select('prod_id')->first();
                } else {
                    $existingChildProduct = Product::where(["company_id" => $companyId, "product_code" => $product_code, "ac4" => $parentProductCode, "is_parent" => $productType])->select('prod_id')->first();
                }


                $existingChildProductId = !empty($existingChildProduct) && isset($existingChildProduct["prod_id"]) ? trim($existingChildProduct["prod_id"]) : "";
                $is_parent = $productType;

                if (empty($clean_description) && !empty($dwProductDesc)) {
                    $desc_status = self::PRODUCT_DESC_DW;
                }


                $dt_pack = !empty($pricingProductItem) && !empty($pricingProductItem["dt_pack"]) && $pricingProductItem["dt_pack"] ? trim($pricingProductItem["dt_pack"]) : "";
                $dt_price = !empty($pricingProductItem) && !empty($pricingProductItem["dt_price"]) && $pricingProductItem["dt_price"] ? trim($pricingProductItem["dt_price"]) : "";
                $dt_type = !empty($pricingProductItem) && !empty($pricingProductItem["dt_type"]) && $pricingProductItem["dt_type"] ? trim($pricingProductItem["dt_type"]) : "";
                $dt_description = !empty($pricingProductItem) && !empty($pricingProductItem["dt_desc"]) && isset($pricingProductItem["dt_desc"]) ? trim($pricingProductItem["dt_desc"]) : "";
                $sm_description = !empty($pricingProductItem) && !empty($pricingProductItem["sm_description"]) && $pricingProductItem["sm_description"] ? trim($pricingProductItem["sm_description"]) : '';
                $sm_description2 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_description2"]) && $pricingProductItem["sm_description2"] ? trim($pricingProductItem["sm_description2"]) : '';
                $sm_description3 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_description3"]) && isset($importData["sm_description3"]) ? trim($importData["sm_description3"]) : "";
                //$sm_analysis_code1 = !empty($pricingProductItem) && isset($pricingProductItem["sm_analysis_code1"]) ? trim($pricingProductItem["sm_analysis_code1"]) : "";
                //$sm_analysis_code2 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_analysis_code2"]) && isset($pricingProductItem["sm_analysis_code2"]) ? trim($pricingProductItem["sm_analysis_code2"]) : "";
                // $sm_analysis_code3 = !empty($pricingProductItem) && !empty($pricingProductItem) && !empty($pricingProductItem["sm_analysis_code3"]) && isset($pricingProductItem["sm_analysis_code3"]) ? trim($pricingProductItem["sm_analysis_code3"]) : "";
                //$sm_analysis_code5 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_analysis_code5"]) && isset($pricingProductItem["sm_analysis_code5"]) ? trim($pricingProductItem["sm_analysis_code5"]) : "";
                //$sm_analysis_code6 = !empty($pricingProductItem["sm_analysis_code6"]) && isset($pricingProductItem["sm_analysis_code6"]) ? trim($pricingProductItem["sm_analysis_code6"]) : "";
                $product_group = !empty($pricingProductItem) && !empty($pricingProductItem["a_prod_group"]) && isset($pricingProductItem["a_prod_group"]) ? trim($pricingProductItem["a_prod_group"]) : "";
                $product_group2 = !empty($pricingProductItem) && !empty($pricingProductItem["a_prod_group2"]) && isset($pricingProductItem["a_prod_group2"]) ? trim($pricingProductItem["a_prod_group2"]) : "";
                $sm_bin_loc = !empty($pricingProductItem) && !empty($pricingProductItem["sm_bin_loc"]) && isset($pricingProductItem["sm_bin_loc"]) ? trim($pricingProductItem["sm_bin_loc"]) : "";
                $vmpp = !empty($pricingProductItem) && !empty($pricingProductItem["vmpp"]) && $pricingProductItem["vmpp"] ? trim($pricingProductItem["vmpp"]) : "";
                $ampp = !empty($pricingProductItem) && !empty($pricingProductItem["ampp"]) && isset($pricingProductItem["ampp"]) ? trim($pricingProductItem["ampp"]) : "";
                $ske_analysis26 = !empty($pricingProductItem) && !empty($pricingProductItem["ske_analysis26"]) && isset($pricingProductItem["ske_analysis26"]) ? trim($pricingProductItem["ske_analysis_26"]) : "";
                $vmpp_description = !empty($pricingProductItem) && !empty($pricingProductItem["vmpp_description"]) && isset($pricingProductItem["vmpp_description"]) ? trim($pricingProductItem["vmpp_description"]) : '';
                $ampp_description = !empty($pricingProductItem) && !empty($pricingProductItem["ampp_description"]) && isset($pricingProductItem["ampp_description"]) ? trim($pricingProductItem["ampp_description"]) : '';

                $bako_ds_scheme = !empty($dwProductItem["Bako_DS_Scheme"]) && isset($dwProductItem["Bako_DS_Scheme"]) ? trim($dwProductItem["Bako_DS_Scheme"]) : "";
                $sedes_pip_code = !empty($dwProductItem["Sedes_PIP_Code"]) && isset($dwProductItem["Sedes_PIP_Code"]) ? trim($dwProductItem["Sedes_PIP_Code"]) : "";
                $dept_code = !empty($dwProductItem["Dept_Code"]) && isset($dwProductItem["Dept_Code"]) ? trim($dwProductItem["Dept_Code"]) : "";
                $supp_prod_code = !empty($dwProductItem["Supp_Prod_Code"]) && isset($dwProductItem["Supp_Prod_Code"]) ? trim($dwProductItem["Supp_Prod_Code"]) : "";
                $temperature = !empty($dwProductItem["Temperature_Type"]) && $dwProductItem["Temperature_Type"] ? trim($dwProductItem["Temperature_Type"]) : "";
                $measured_ind = !empty($dwProductItem["Measured_Ind"]) && isset($dwProductItem["Measured_Ind"]) ? trim($dwProductItem["Measured_Ind"]) : "";
                $zero_discount_ind = !empty($dwProductItem["Zero_Discount_Ind"]) && isset($dwProductItem["Zero_Discount_Ind"]) ? trim($dwProductItem["Zero_Discount_Ind"]) : "";
                $own_brand_ind = !empty($dwProductItem["Own_Brand_Ind"]) && isset($dwProductItem["Own_Brand_Ind"]) ? trim($dwProductItem["Own_Brand_Ind"]) : "";
                $cust_brand_ind = !empty($dwProductItem["Cust_Brand_Ind"]) && isset($dwProductItem["Cust_Brand_Ind"]) ? trim($dwProductItem["Cust_Brand_Ind"]) : "";
                $list_price = !empty($dwProductItem["List_Price"]) && $dwProductItem["List_Price"] ? trim($dwProductItem["List_Price"]) : "";
                $pack_size = !empty($dwProductItem["Pack_Size"]) && $dwProductItem["Pack_Size"] ? trim($dwProductItem["Pack_Size"]) : "";
                $pack_desc = !empty($dwProductItem["Pack_Desc"]) && $dwProductItem["Pack_Desc"] ? trim($dwProductItem["Pack_Desc"]) : "";
                $special_discount = !empty($dwProductItem["Special_Discount"]) && $dwProductItem["Special_Discount"] ? trim($dwProductItem["Special_Discount"]) : "";
                $case_weight = !empty($dwProductItem["Case_Weight"]) && $dwProductItem["Case_Weight"] ? trim($dwProductItem["Case_Weight"]) : "";
                $case_volume = !empty($dwProductItem["Case_Volume"]) && $dwProductItem["Case_Volume"] ? trim($dwProductItem["Case_Volume"]) : "";
                $case_desc = !empty($dwProductItem["Case_Desc"]) && $dwProductItem["Case_Desc"] ? trim($dwProductItem["Case_Desc"]) : "";
                $bin_location = !empty($dwProductItem["Bin_Location"]) && $dwProductItem["Bin_Location"] ? trim($dwProductItem["Bin_Location"]) : "";
                $last_purchase_cost = !empty($dwProductItem["Last_Purchase_Cost"]) && $dwProductItem["Last_Purchase_Cost"] ? trim($dwProductItem["Last_Purchase_Cost"]) : "";
                $standard_cost = !empty($dwProductItem["Standard_Cost"]) && $dwProductItem["Standard_Cost"] ? trim($dwProductItem["Standard_Cost"]) : "";
                $min_shelf_life = !empty($dwProductItem["Min_Shelf_Life"]) && $dwProductItem["Min_Shelf_Life"] ? trim($dwProductItem["Min_Shelf_Life"]) : "";
                $full_pal_qty = !empty($dwProductItem["Full_Pal_Qty"]) && $dwProductItem["Full_Pal_Qty"] ? trim($dwProductItem["Full_Pal_Qty"]) : "";
                $avg_usage = !empty($dwProductItem["Avg_Usage"]) && $dwProductItem["Avg_Usage"] ? trim($dwProductItem["Avg_Usage"]) : NULL;
                $avg_usage_uom = !empty($dwProductItem["Avg_USage_UOM"]) && $dwProductItem["Avg_USage_UOM"] ? trim($dwProductItem["Avg_USage_UOM"]) : "";
                $unit_weight = !empty($dwProductItem["Unit_Weight"]) && $dwProductItem["Unit_Weight"] ? trim($dwProductItem["Unit_Weight"]) : "";
                $sub_group_1 = !empty($dwProductItem["Sub_Group_1"]) && isset($dwProductItem["Sub_Group_1"]) ? trim($dwProductItem["Sub_Group_1"]) : "";
                $sub_group_2 = !empty($dwProductItem["Sub_Group_2"]) && isset($dwProductItem["Sub_Group_2"]) ? trim($dwProductItem["Sub_Group_2"]) : "";
                $sub_group_3 = !empty($dwProductItem["Sub_Group_3"]) && isset($dwProductItem["Sub_Group_3"]) ? trim($dwProductItem["Sub_Group_3"]) : "";
                $sub_group_4 = !empty($dwProductItem["Sub_Group_4"]) && isset($dwProductItem["Sub_Group_4"]) ? trim($dwProductItem["Sub_Group_4"]) : "";
                $sub_group_5 = !empty($dwProductItem["Sub_Group_5"]) && isset($dwProductItem["Sub_Group_5"]) ? trim($dwProductItem["Sub_Group_5"]) : "";
                $sub_group_6 = !empty($dwProductItem["Sub_Group_6"]) && isset($dwProductItem["Sub_Group_6"]) ? trim($dwProductItem["Sub_Group_6"]) : "";
                $additional1 = !empty($dwProductItem["Additional_1"]) && isset($dwProductItem["Additional_1"]) ? trim($dwProductItem["Additional_1"]) : "";
                $additional2 = !empty($dwProductItem["Additional_2"]) && isset($dwProductItem["Additional_2"]) ? trim($dwProductItem["Additional_2"]) : "";
                $additional3 = !empty($dwProductItem["Additional_3"]) && isset($dwProductItem["Additional_3"]) ? trim($dwProductItem["Additional_3"]) : "";
                $additional4 = !empty($dwProductItem["Additional_4"]) && isset($dwProductItem["Additional_4"]) ? trim($dwProductItem["Additional_4"]) : "";
                $additional5 = !empty($dwProductItem["Additional_5"]) && isset($dwProductItem["Additional_5"]) ? trim($dwProductItem["Additional_5"]) : "";
                $additional6 = !empty($dwProductItem["Additional_6"]) && isset($dwProductItem["Additional_6"]) ? trim($dwProductItem["Additional_6"]) : "";
                $is_active = !empty($dwProductItem["Is_Active"]) && isset($dwProductItem["Is_Active"]) ? trim($dwProductItem["Is_Active"]) : "";

                $ac1 = !empty($dwProductItem["Product_AC_1"]) && isset($dwProductItem["Product_AC_1"]) ? trim($dwProductItem["Product_AC_1"]) : "";
                $ac2 = !empty($dwProductItem["Product_AC_2"]) && isset($dwProductItem["Product_AC_2"]) ? trim($dwProductItem["Product_AC_2"]) : "";
                $ac3 = !empty($dwProductItem["Product_AC_3"]) && isset($dwProductItem["Product_AC_3"]) ? trim($dwProductItem["Product_AC_3"]) : "";
                $ac4 = !empty($dwProductItem["Product_AC_4"]) && isset($dwProductItem["Product_AC_4"]) ? trim($dwProductItem["Product_AC_4"]) : "";
                $ac5 = !empty($dwProductItem["Product_AC_5"]) && isset($dwProductItem["Product_AC_5"]) ? trim($dwProductItem["Product_AC_5"]) : "";
                $ac6 = !empty($dwProductItem["Product_AC_6"]) && isset($dwProductItem["Product_AC_6"]) ? trim($dwProductItem["Product_AC_6"]) : "";
                $dw_status = !empty($dwProductItem["Product_Status"]) && isset($dwProductItem["Product_Status"]) ? trim($dwProductItem["Product_Status"]) : "";
                $company_id = !empty($dwProductItem["Company_Id"]) && $dwProductItem["Company_Id"] ? trim($dwProductItem["Company_Id"]) : "";
                $product_group_id = !empty($dwProductItem["ProductGroup_Id"]) && isset($dwProductItem["ProductGroup_Id"]) ? trim($dwProductItem["ProductGroup_Id"]) : NULL;
                $preferred_supplier_Id = !empty($dwProductItem["Preferred_Supplier_Id"]) && isset($dwProductItem["Preferred_Supplier_Id"]) ? trim($dwProductItem["Preferred_Supplier_Id"]) : NULL;
                $vat_id = !empty($dwProductItem["Vat_Id"]) && isset($dwProductItem["Vat_Id"]) ? trim($dwProductItem["Vat_Id"]) : "";
                $list_currency_id = !empty($dwProductItem["List_Currency_Id"]) && isset($dwProductItem["List_Currency_Id"]) ? trim($dwProductItem["List_Currency_Id"]) : NULL;
                $product_type = !empty($dwProductItem["Product_Type"]) && isset($dwProductItem["Product_Type"]) ? trim($dwProductItem["Product_Type"]) : "";
                $prod_status = self::COMPLETE;
                $data = array(
                    "product_code" => $product_code,
                    "dt_pack" => $dt_pack,
                    "dt_type" => $dt_type,
                    "dt_price" => $dt_price,
                    "dt_description" => $dt_description,
                    "sm_description" => $sm_description,
                    "sm_description2" => $sm_description2,
                    "sm_description3" => $sm_description3,
                    // "sm_analysis_code1" => $sm_analysis_code1,
                    //"sm_analysis_code2" => $sm_analysis_code2,
                    // "sm_analysis_code3" => $sm_analysis_code3,
                    //"sm_analysis_code5" => $sm_analysis_code5,
                    //"sm_analysis_code6" => $sm_analysis_code6,
                    "product_group" => $product_group,
                    "product_group2" => $product_group2,
                    "sm_bin_loc" => $sm_bin_loc,
                    "vmpp" => $vmpp,
                    "ampp" => $ampp,
                    "ske_analysis26" => $ske_analysis26,
                    "vmpp_description" => $vmpp_description,
                    "ampp_description" => $ampp_description,
                    "clean_description" => $clean_description,
//                   //is_parent:: 1 == Parent, 0 == Child
                    "is_parent" => $is_parent,
                    "onboarding_as" => 'Initial migration and update',
                    "desc_status" => $desc_status,
                    "company_id" => $company_id,
                    "product_group_id" => $product_group_id,
                    "preferred_supplier_Id" => $preferred_supplier_Id,
                    "vat_id" => $vat_id,
                    "list_currency_id" => $list_currency_id,
                    "product_desc" => $dwProductDesc,
                    "product_type" => $product_type,
                    "ac1" => $ac1,
                    "ac2" => $ac2,
                    "ac3" => $ac3,
                    "ac4" => $parentProductCode,
                    "ac5" => $ac5,
                    "ac6" => $ac6,
                    "bako_ds_scheme" => $bako_ds_scheme,
                    "sedes_pip_code" => $sedes_pip_code,
                    "dept_code" => $dept_code,
                    "supp_prod_code" => $supp_prod_code,
                    "temperature" => $temperature,
                    "measured_ind" => $measured_ind,
                    "zero_discount_ind" => $zero_discount_ind,
                    "own_brand_ind" => $own_brand_ind,
                    "cust_brand_ind" => $cust_brand_ind,
                    "list_price" => $list_price,
                    "pack_size" => $pack_size,
                    "pack_desc" => $pack_desc,
                    "special_discount" => $special_discount,
                    "case_weight" => $case_weight,
                    "case_volume" => $case_volume,
                    "case_desc" => $case_desc,
                    "bin_location" => $bin_location,
                    "last_purchase_cost" => $last_purchase_cost,
                    "standard_cost" => $standard_cost,
                    "min_shelf_life" => $min_shelf_life,
                    "full_pal_qty" => $full_pal_qty,
                    "avg_usage" => $avg_usage,
                    "avg_usage_uom" => $avg_usage_uom,
                    "unit_weight" => $unit_weight,
                    "sub_group_1" => $sub_group_1,
                    "sub_group_2" => $sub_group_2,
                    "sub_group_3" => $sub_group_3,
                    "sub_group_4" => $sub_group_4,
                    "sub_group_5" => $sub_group_5,
                    "sub_group_6" => $sub_group_6,
                    "additional1" => $additional1,
                    "additional2" => $additional2,
                    "additional3" => $additional3,
                    "additional4" => $additional4,
                    "additional5" => $additional5,
                    "additional6" => $additional6,
                    "dw_status" => $dw_status,
                    "prod_status" => $prod_status,
                    "is_active" => $is_active,
                    "inserted_by" => $userId,
                    "lastchanged_by" => $userId,
                    "updated_at" => now());

                if (!empty($existingChildProductId)) {
                    DB::table('products')
                            ->where(["prod_id" => $existingChildProductId])
                            ->update($data);
                } else {

                    $newChildData[] = array(
                        "product_code" => $product_code,
                        "dt_pack" => $dt_pack,
                        "dt_type" => $dt_type,
                        "dt_price" => $dt_price,
                        "dt_description" => $dt_description,
                        "sm_description" => $sm_description,
                        "sm_description2" => $sm_description2,
                        "sm_description3" => $sm_description3,
                        //"sm_analysis_code1" => $sm_analysis_code1,
                        //"sm_analysis_code2" => $sm_analysis_code2,
                        //"sm_analysis_code3" => $sm_analysis_code3,
                        //"sm_analysis_code5" => $sm_analysis_code5,
                        //"sm_analysis_code6" => $sm_analysis_code6,
                        "product_group" => $product_group,
                        "product_group2" => $product_group2,
                        "sm_bin_loc" => $sm_bin_loc,
                        "vmpp" => $vmpp,
                        "ampp" => $ampp,
                        "ske_analysis26" => $ske_analysis26,
                        "vmpp_description" => $vmpp_description,
                        "ampp_description" => $ampp_description,
                        "clean_description" => $clean_description,
//                   //is_parent:: 1 == Parent, 0 == Child
                        "is_parent" => $is_parent,
                        "onboarding_as" => 'Initial migration',
                        "desc_status" => $desc_status,
                        "company_id" => $company_id,
                        "product_group_id" => $product_group_id,
                        "preferred_supplier_Id" => $preferred_supplier_Id,
                        "vat_id" => $vat_id,
                        "list_currency_id" => $list_currency_id,
                        "product_desc" => $dwProductDesc,
                        "product_type" => $product_type,
                        "ac1" => $ac1,
                        "ac2" => $ac2,
                        "ac3" => $ac3,
                        "temp_ac4" => $temp_ac4,
                        "ac4" => $parentProductCode,
                        "ac5" => $ac5,
                        "ac6" => $ac6,
                        "bako_ds_scheme" => $bako_ds_scheme,
                        "sedes_pip_code" => $sedes_pip_code,
                        "dept_code" => $dept_code,
                        "supp_prod_code" => $supp_prod_code,
                        "temperature" => $temperature,
                        "measured_ind" => $measured_ind,
                        "zero_discount_ind" => $zero_discount_ind,
                        "own_brand_ind" => $own_brand_ind,
                        "cust_brand_ind" => $cust_brand_ind,
                        "list_price" => $list_price,
                        "pack_size" => $pack_size,
                        "pack_desc" => $pack_desc,
                        "special_discount" => $special_discount,
                        "case_weight" => $case_weight,
                        "case_volume" => $case_volume,
                        "case_desc" => $case_desc,
                        "bin_location" => $bin_location,
                        "last_purchase_cost" => $last_purchase_cost,
                        "standard_cost" => $standard_cost,
                        "min_shelf_life" => $min_shelf_life,
                        "full_pal_qty" => $full_pal_qty,
                        "avg_usage" => $avg_usage,
                        "avg_usage_uom" => $avg_usage_uom,
                        "unit_weight" => $unit_weight,
                        "sub_group_1" => $sub_group_1,
                        "sub_group_2" => $sub_group_2,
                        "sub_group_3" => $sub_group_3,
                        "sub_group_4" => $sub_group_4,
                        "sub_group_5" => $sub_group_5,
                        "sub_group_6" => $sub_group_6,
                        "additional1" => $additional1,
                        "additional2" => $additional2,
                        "additional3" => $additional3,
                        "additional4" => $additional4,
                        "additional5" => $additional5,
                        "additional6" => $additional6,
                        "is_active" => $is_active,
                        "dw_status" => $dw_status,
                        "prod_status" => $prod_status,
                        "created_at" => now(),
                        "inserted_by" => $userId,
                        "lastchanged_by" => $userId,
                        "updated_at" => now());
                }
            }
        }


        if (!empty($newChildData)) {
            foreach (array_chunk($newChildData, (2100 / 91) - 2) as $chunk) {
                Product::insert($chunk);
            }
        }
    }

    /**
     * Analize and stores child products
     *
     *
     * */
    private static function storeChildProducts($dwProducts, $productType = NULL)
    {
        $desc_status = $temp_ac4 = "";
        $newChildData = $data = [];
        $userId = 1;

        foreach ($dwProducts as $dwProductItem) {

            $product_code = !empty($dwProductItem) && isset($dwProductItem["Product_Code"]) ? trim($dwProductItem["Product_Code"]) : "";

            $parentProductCode = !empty($dwProductItem) && isset($dwProductItem["Product_AC_4"]) ? trim($dwProductItem["Product_AC_4"]) : "";

            $pricingProductItem = ClDesProduct::where(["sm_analysis_code4" => $parentProductCode])->first();

            $clean_description = !empty($pricingProductItem) && isset($pricingProductItem["clean_description"]) ? trim($pricingProductItem["clean_description"]) : "";
            $dwProductDesc = !empty($dwProductItem["Product_Desc"]) && isset($dwProductItem["Product_Desc"]) ? trim($dwProductItem["Product_Desc"]) : "";
            $companyId = !empty($dwProductItem["Company_Id"]) && $dwProductItem["Company_Id"] ? trim($dwProductItem["Company_Id"]) : "";

            $existingChildProduct = Product::where(["company_id" => $companyId, "product_code" => $product_code, "ac4" => $parentProductCode, "is_parent" => $productType])->select('prod_id')->first();

            $existingChildProductId = !empty($existingChildProduct) && isset($existingChildProduct["prod_id"]) ? trim($existingChildProduct["prod_id"]) : "";
            $is_parent = $productType;

            if (empty($clean_description) && !empty($dwProductDesc)) {
                $desc_status = self::PRODUCT_DESC_DW;
            }


            $dt_pack = !empty($pricingProductItem) && !empty($pricingProductItem["dt_pack"]) && $pricingProductItem["dt_pack"] ? trim($pricingProductItem["dt_pack"]) : "";
            $dt_price = !empty($pricingProductItem) && !empty($pricingProductItem["dt_price"]) && $pricingProductItem["dt_price"] ? trim($pricingProductItem["dt_price"]) : "";
            $dt_type = !empty($pricingProductItem) && !empty($pricingProductItem["dt_type"]) && $pricingProductItem["dt_type"] ? trim($pricingProductItem["dt_type"]) : "";
            $dt_description = !empty($pricingProductItem) && !empty($pricingProductItem["dt_desc"]) && isset($pricingProductItem["dt_desc"]) ? trim($pricingProductItem["dt_desc"]) : "";
            $sm_description = !empty($pricingProductItem) && !empty($pricingProductItem["sm_description"]) && $pricingProductItem["sm_description"] ? trim($pricingProductItem["sm_description"]) : '';
            $sm_description2 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_description2"]) && $pricingProductItem["sm_description2"] ? trim($pricingProductItem["sm_description2"]) : '';
            $sm_description3 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_description3"]) && isset($importData["sm_description3"]) ? trim($importData["sm_description3"]) : "";
            // $sm_analysis_code1 = !empty($pricingProductItem) && isset($pricingProductItem["sm_analysis_code1"]) ? trim($pricingProductItem["sm_analysis_code1"]) : "";
            // $sm_analysis_code2 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_analysis_code2"]) && isset($pricingProductItem["sm_analysis_code2"]) ? trim($pricingProductItem["sm_analysis_code2"]) : "";
            // $sm_analysis_code3 = !empty($pricingProductItem) && !empty($pricingProductItem) && !empty($pricingProductItem["sm_analysis_code3"]) && isset($pricingProductItem["sm_analysis_code3"]) ? trim($pricingProductItem["sm_analysis_code3"]) : "";
            // $sm_analysis_code5 = !empty($pricingProductItem) && !empty($pricingProductItem["sm_analysis_code5"]) && isset($pricingProductItem["sm_analysis_code5"]) ? trim($pricingProductItem["sm_analysis_code5"]) : "";
            //  $sm_analysis_code6 = !empty($pricingProductItem["sm_analysis_code6"]) && isset($pricingProductItem["sm_analysis_code6"]) ? trim($pricingProductItem["sm_analysis_code6"]) : "";
            $product_group = !empty($pricingProductItem) && !empty($pricingProductItem["a_prod_group"]) && isset($pricingProductItem["a_prod_group"]) ? trim($pricingProductItem["a_prod_group"]) : "";
            $product_group2 = !empty($pricingProductItem) && !empty($pricingProductItem["a_prod_group2"]) && isset($pricingProductItem["a_prod_group2"]) ? trim($pricingProductItem["a_prod_group2"]) : "";
            $sm_bin_loc = !empty($pricingProductItem) && !empty($pricingProductItem["sm_bin_loc"]) && isset($pricingProductItem["sm_bin_loc"]) ? trim($pricingProductItem["sm_bin_loc"]) : "";
            $vmpp = !empty($pricingProductItem) && !empty($pricingProductItem["vmpp"]) && $pricingProductItem["vmpp"] ? trim($pricingProductItem["vmpp"]) : "";
            $ampp = !empty($pricingProductItem) && !empty($pricingProductItem["ampp"]) && isset($pricingProductItem["ampp"]) ? trim($pricingProductItem["ampp"]) : "";
            $ske_analysis26 = !empty($pricingProductItem) && !empty($pricingProductItem["ske_analysis26"]) && isset($pricingProductItem["ske_analysis26"]) ? trim($pricingProductItem["ske_analysis_26"]) : "";
            $vmpp_description = !empty($pricingProductItem) && !empty($pricingProductItem["vmpp_description"]) && isset($pricingProductItem["vmpp_description"]) ? trim($pricingProductItem["vmpp_description"]) : '';
            $ampp_description = !empty($pricingProductItem) && !empty($pricingProductItem["ampp_description"]) && isset($pricingProductItem["ampp_description"]) ? trim($pricingProductItem["ampp_description"]) : '';

            $bako_ds_scheme = !empty($dwProductItem["Bako_DS_Scheme"]) && isset($dwProductItem["Bako_DS_Scheme"]) ? trim($dwProductItem["Bako_DS_Scheme"]) : "";
            $sedes_pip_code = !empty($dwProductItem["Sedes_PIP_Code"]) && isset($dwProductItem["Sedes_PIP_Code"]) ? trim($dwProductItem["Sedes_PIP_Code"]) : "";
            $dept_code = !empty($dwProductItem["Dept_Code"]) && isset($dwProductItem["Dept_Code"]) ? trim($dwProductItem["Dept_Code"]) : "";
            $supp_prod_code = !empty($dwProductItem["Supp_Prod_Code"]) && isset($dwProductItem["Supp_Prod_Code"]) ? trim($dwProductItem["Supp_Prod_Code"]) : "";
            $temperature = !empty($dwProductItem["Temperature_Type"]) && $dwProductItem["Temperature_Type"] ? trim($dwProductItem["Temperature_Type"]) : "";
            $measured_ind = !empty($dwProductItem["Measured_Ind"]) && isset($dwProductItem["Measured_Ind"]) ? trim($dwProductItem["Measured_Ind"]) : "";
            $zero_discount_ind = !empty($dwProductItem["Zero_Discount_Ind"]) && isset($dwProductItem["Zero_Discount_Ind"]) ? trim($dwProductItem["Zero_Discount_Ind"]) : "";
            $own_brand_ind = !empty($dwProductItem["Own_Brand_Ind"]) && isset($dwProductItem["Own_Brand_Ind"]) ? trim($dwProductItem["Own_Brand_Ind"]) : "";
            $cust_brand_ind = !empty($dwProductItem["Cust_Brand_Ind"]) && isset($dwProductItem["Cust_Brand_Ind"]) ? trim($dwProductItem["Cust_Brand_Ind"]) : "";
            $list_price = !empty($dwProductItem["List_Price"]) && $dwProductItem["List_Price"] ? trim($dwProductItem["List_Price"]) : "";
            $pack_size = !empty($dwProductItem["Pack_Size"]) && $dwProductItem["Pack_Size"] ? trim($dwProductItem["Pack_Size"]) : "";
            $pack_desc = !empty($dwProductItem["Pack_Desc"]) && $dwProductItem["Pack_Desc"] ? trim($dwProductItem["Pack_Desc"]) : "";
            $special_discount = !empty($dwProductItem["Special_Discount"]) && $dwProductItem["Special_Discount"] ? trim($dwProductItem["Special_Discount"]) : "";
            $case_weight = !empty($dwProductItem["Case_Weight"]) && $dwProductItem["Case_Weight"] ? trim($dwProductItem["Case_Weight"]) : "";
            $case_volume = !empty($dwProductItem["Case_Volume"]) && $dwProductItem["Case_Volume"] ? trim($dwProductItem["Case_Volume"]) : "";
            $case_desc = !empty($dwProductItem["Case_Desc"]) && $dwProductItem["Case_Desc"] ? trim($dwProductItem["Case_Desc"]) : "";
            $bin_location = !empty($dwProductItem["Bin_Location"]) && $dwProductItem["Bin_Location"] ? trim($dwProductItem["Bin_Location"]) : "";
            $last_purchase_cost = !empty($dwProductItem["Last_Purchase_Cost"]) && $dwProductItem["Last_Purchase_Cost"] ? trim($dwProductItem["Last_Purchase_Cost"]) : "";
            $standard_cost = !empty($dwProductItem["Standard_Cost"]) && $dwProductItem["Standard_Cost"] ? trim($dwProductItem["Standard_Cost"]) : "";
            $min_shelf_life = !empty($dwProductItem["Min_Shelf_Life"]) && $dwProductItem["Min_Shelf_Life"] ? trim($dwProductItem["Min_Shelf_Life"]) : "";
            $full_pal_qty = !empty($dwProductItem["Full_Pal_Qty"]) && $dwProductItem["Full_Pal_Qty"] ? trim($dwProductItem["Full_Pal_Qty"]) : "";
            $avg_usage = !empty($dwProductItem["Avg_Usage"]) && $dwProductItem["Avg_Usage"] ? trim($dwProductItem["Avg_Usage"]) : NULL;
            $avg_usage_uom = !empty($dwProductItem["Avg_USage_UOM"]) && $dwProductItem["Avg_USage_UOM"] ? trim($dwProductItem["Avg_USage_UOM"]) : "";
            $unit_weight = !empty($dwProductItem["Unit_Weight"]) && $dwProductItem["Unit_Weight"] ? trim($dwProductItem["Unit_Weight"]) : "";
            $sub_group_1 = !empty($dwProductItem["Sub_Group_1"]) && isset($dwProductItem["Sub_Group_1"]) ? trim($dwProductItem["Sub_Group_1"]) : "";
            $sub_group_2 = !empty($dwProductItem["Sub_Group_2"]) && isset($dwProductItem["Sub_Group_2"]) ? trim($dwProductItem["Sub_Group_2"]) : "";
            $sub_group_3 = !empty($dwProductItem["Sub_Group_3"]) && isset($dwProductItem["Sub_Group_3"]) ? trim($dwProductItem["Sub_Group_3"]) : "";
            $sub_group_4 = !empty($dwProductItem["Sub_Group_4"]) && isset($dwProductItem["Sub_Group_4"]) ? trim($dwProductItem["Sub_Group_4"]) : "";
            $sub_group_5 = !empty($dwProductItem["Sub_Group_5"]) && isset($dwProductItem["Sub_Group_5"]) ? trim($dwProductItem["Sub_Group_5"]) : "";
            $sub_group_6 = !empty($dwProductItem["Sub_Group_6"]) && isset($dwProductItem["Sub_Group_6"]) ? trim($dwProductItem["Sub_Group_6"]) : "";
            $additional1 = !empty($dwProductItem["Additional_1"]) && isset($dwProductItem["Additional_1"]) ? trim($dwProductItem["Additional_1"]) : "";
            $additional2 = !empty($dwProductItem["Additional_2"]) && isset($dwProductItem["Additional_2"]) ? trim($dwProductItem["Additional_2"]) : "";
            $additional3 = !empty($dwProductItem["Additional_3"]) && isset($dwProductItem["Additional_3"]) ? trim($dwProductItem["Additional_3"]) : "";
            $additional4 = !empty($dwProductItem["Additional_4"]) && isset($dwProductItem["Additional_4"]) ? trim($dwProductItem["Additional_4"]) : "";
            $additional5 = !empty($dwProductItem["Additional_5"]) && isset($dwProductItem["Additional_5"]) ? trim($dwProductItem["Additional_5"]) : "";
            $additional6 = !empty($dwProductItem["Additional_6"]) && isset($dwProductItem["Additional_6"]) ? trim($dwProductItem["Additional_6"]) : "";
            $is_active = !empty($dwProductItem["Is_Active"]) && isset($dwProductItem["Is_Active"]) ? trim($dwProductItem["Is_Active"]) : "";

            $ac1 = !empty($dwProductItem["Product_AC_1"]) && isset($dwProductItem["Product_AC_1"]) ? trim($dwProductItem["Product_AC_1"]) : "";
            $ac2 = !empty($dwProductItem["Product_AC_2"]) && isset($dwProductItem["Product_AC_2"]) ? trim($dwProductItem["Product_AC_2"]) : "";
            $ac3 = !empty($dwProductItem["Product_AC_3"]) && isset($dwProductItem["Product_AC_3"]) ? trim($dwProductItem["Product_AC_3"]) : "";
            $ac4 = !empty($dwProductItem["Product_AC_4"]) && isset($dwProductItem["Product_AC_4"]) ? trim($dwProductItem["Product_AC_4"]) : "";
            $ac5 = !empty($dwProductItem["Product_AC_5"]) && isset($dwProductItem["Product_AC_5"]) ? trim($dwProductItem["Product_AC_5"]) : "";
            $ac6 = !empty($dwProductItem["Product_AC_6"]) && isset($dwProductItem["Product_AC_6"]) ? trim($dwProductItem["Product_AC_6"]) : "";
            $dw_status = !empty($dwProductItem["Product_Status"]) && isset($dwProductItem["Product_Status"]) ? trim($dwProductItem["Product_Status"]) : "";
            $company_id = !empty($dwProductItem["Company_Id"]) && $dwProductItem["Company_Id"] ? trim($dwProductItem["Company_Id"]) : "";
            $product_group_id = !empty($dwProductItem["ProductGroup_Id"]) && isset($dwProductItem["ProductGroup_Id"]) ? trim($dwProductItem["ProductGroup_Id"]) : NULL;
            $preferred_supplier_Id = !empty($dwProductItem["Preferred_Supplier_Id"]) && isset($dwProductItem["Preferred_Supplier_Id"]) ? trim($dwProductItem["Preferred_Supplier_Id"]) : NULL;
            $vat_id = !empty($dwProductItem["Vat_Id"]) && isset($dwProductItem["Vat_Id"]) ? trim($dwProductItem["Vat_Id"]) : "";
            $list_currency_id = !empty($dwProductItem["List_Currency_Id"]) && isset($dwProductItem["List_Currency_Id"]) ? trim($dwProductItem["List_Currency_Id"]) : NULL;
            $product_type = !empty($dwProductItem["Product_Type"]) && isset($dwProductItem["Product_Type"]) ? trim($dwProductItem["Product_Type"]) : "";
            $prod_status = self::COMPLETE;
            $data = array(
                "product_code" => $product_code,
                "dt_pack" => $dt_pack,
                "dt_type" => $dt_type,
                "dt_price" => $dt_price,
                "dt_description" => $dt_description,
                "sm_description" => $sm_description,
                "sm_description2" => $sm_description2,
                "sm_description3" => $sm_description3,
                // "sm_analysis_code1" => $sm_analysis_code1,
                // "sm_analysis_code2" => $sm_analysis_code2,
                // "sm_analysis_code3" => $sm_analysis_code3,
                // "sm_analysis_code5" => $sm_analysis_code5,
                // "sm_analysis_code6" => $sm_analysis_code6,
                "product_group" => $product_group,
                "product_group2" => $product_group2,
                "sm_bin_loc" => $sm_bin_loc,
                "vmpp" => $vmpp,
                "ampp" => $ampp,
                "ske_analysis26" => $ske_analysis26,
                "vmpp_description" => $vmpp_description,
                "ampp_description" => $ampp_description,
                "clean_description" => $clean_description,
//                   //is_parent:: 1 == Parent, 0 == Child
                "is_parent" => $is_parent,
                "onboarding_as" => 'Initial migration and update',
                "desc_status" => $desc_status,
                "company_id" => $company_id,
                "product_group_id" => $product_group_id,
                "preferred_supplier_Id" => $preferred_supplier_Id,
                "vat_id" => $vat_id,
                "list_currency_id" => $list_currency_id,
                "product_desc" => $dwProductDesc,
                "product_type" => $product_type,
                "ac1" => $ac1,
                "ac2" => $ac2,
                "ac3" => $ac3,
                "ac4" => $parentProductCode,
                "ac5" => $ac5,
                "ac6" => $ac6,
                "bako_ds_scheme" => $bako_ds_scheme,
                "sedes_pip_code" => $sedes_pip_code,
                "dept_code" => $dept_code,
                "supp_prod_code" => $supp_prod_code,
                "temperature" => $temperature,
                "measured_ind" => $measured_ind,
                "zero_discount_ind" => $zero_discount_ind,
                "own_brand_ind" => $own_brand_ind,
                "cust_brand_ind" => $cust_brand_ind,
                "list_price" => $list_price,
                "pack_size" => $pack_size,
                "pack_desc" => $pack_desc,
                "special_discount" => $special_discount,
                "case_weight" => $case_weight,
                "case_volume" => $case_volume,
                "case_desc" => $case_desc,
                "bin_location" => $bin_location,
                "last_purchase_cost" => $last_purchase_cost,
                "standard_cost" => $standard_cost,
                "min_shelf_life" => $min_shelf_life,
                "full_pal_qty" => $full_pal_qty,
                "avg_usage" => $avg_usage,
                "avg_usage_uom" => $avg_usage_uom,
                "unit_weight" => $unit_weight,
                "sub_group_1" => $sub_group_1,
                "sub_group_2" => $sub_group_2,
                "sub_group_3" => $sub_group_3,
                "sub_group_4" => $sub_group_4,
                "sub_group_5" => $sub_group_5,
                "sub_group_6" => $sub_group_6,
                "additional1" => $additional1,
                "additional2" => $additional2,
                "additional3" => $additional3,
                "additional4" => $additional4,
                "additional5" => $additional5,
                "additional6" => $additional6,
                "is_active" => $is_active,
                "prod_status" => $prod_status,
                "dw_status" => $dw_status,
                "inserted_by" => $userId,
                "lastchanged_by" => $userId,
                "updated_at" => now());

            if (!empty($existingChildProductId)) {
                DB::table('products')
                        ->where(["prod_id" => $existingChildProductId])
                        ->update($data);
            } else {

                $newChildData[] = array(
                    "product_code" => $product_code,
                    "dt_pack" => $dt_pack,
                    "dt_type" => $dt_type,
                    "dt_price" => $dt_price,
                    "dt_description" => $dt_description,
                    "sm_description" => $sm_description,
                    "sm_description2" => $sm_description2,
                    "sm_description3" => $sm_description3,
                    // "sm_analysis_code1" => $sm_analysis_code1,
                    //  "sm_analysis_code2" => $sm_analysis_code2,
                    //"sm_analysis_code3" => $sm_analysis_code3,
                    //  "sm_analysis_code5" => $sm_analysis_code5,
                    //  "sm_analysis_code6" => $sm_analysis_code6,
                    "product_group" => $product_group,
                    "product_group2" => $product_group2,
                    "sm_bin_loc" => $sm_bin_loc,
                    "vmpp" => $vmpp,
                    "ampp" => $ampp,
                    "ske_analysis26" => $ske_analysis26,
                    "vmpp_description" => $vmpp_description,
                    "ampp_description" => $ampp_description,
                    "clean_description" => $clean_description,
//                   //is_parent:: 1 == Parent, 0 == Child
                    "is_parent" => $is_parent,
                    "onboarding_as" => 'Initial migration',
                    "desc_status" => $desc_status,
                    "company_id" => $company_id,
                    "product_group_id" => $product_group_id,
                    "preferred_supplier_Id" => $preferred_supplier_Id,
                    "vat_id" => $vat_id,
                    "list_currency_id" => $list_currency_id,
                    "product_desc" => $dwProductDesc,
                    "product_type" => $product_type,
                    "ac1" => $ac1,
                    "ac2" => $ac2,
                    "ac3" => $ac3,
                    "temp_ac4" => $temp_ac4,
                    "ac4" => $parentProductCode,
                    "ac5" => $ac5,
                    "ac6" => $ac6,
                    "bako_ds_scheme" => $bako_ds_scheme,
                    "sedes_pip_code" => $sedes_pip_code,
                    "dept_code" => $dept_code,
                    "supp_prod_code" => $supp_prod_code,
                    "temperature" => $temperature,
                    "measured_ind" => $measured_ind,
                    "zero_discount_ind" => $zero_discount_ind,
                    "own_brand_ind" => $own_brand_ind,
                    "cust_brand_ind" => $cust_brand_ind,
                    "list_price" => $list_price,
                    "pack_size" => $pack_size,
                    "pack_desc" => $pack_desc,
                    "special_discount" => $special_discount,
                    "case_weight" => $case_weight,
                    "case_volume" => $case_volume,
                    "case_desc" => $case_desc,
                    "bin_location" => $bin_location,
                    "last_purchase_cost" => $last_purchase_cost,
                    "standard_cost" => $standard_cost,
                    "min_shelf_life" => $min_shelf_life,
                    "full_pal_qty" => $full_pal_qty,
                    "avg_usage" => $avg_usage,
                    "avg_usage_uom" => $avg_usage_uom,
                    "unit_weight" => $unit_weight,
                    "sub_group_1" => $sub_group_1,
                    "sub_group_2" => $sub_group_2,
                    "sub_group_3" => $sub_group_3,
                    "sub_group_4" => $sub_group_4,
                    "sub_group_5" => $sub_group_5,
                    "sub_group_6" => $sub_group_6,
                    "additional1" => $additional1,
                    "additional2" => $additional2,
                    "additional3" => $additional3,
                    "additional4" => $additional4,
                    "additional5" => $additional5,
                    "additional6" => $additional6,
                    "is_active" => $is_active,
                    "prod_status" => $prod_status,
                    "dw_status" => $dw_status,
                    "created_at" => now(),
                    "inserted_by" => $userId,
                    "lastchanged_by" => $userId,
                    "updated_at" => now());
            }
        }

        if (!empty($newChildData)) {
            foreach (array_chunk($newChildData, (2100 / 91) - 2) as $chunk) {
                Product::insert($chunk);
            }
        }
    }

    /**
     * Search Product by keyword
     *
     * @param string $keyword The Term|Keyword
     *
     * @return \Illuminate\Http\Response
     */
  public static function searchProduct($keyword)
    {
        $keywordLower = strtolower($keyword);
        $dtpack = '';
        $keywordArray = $products = $productsA = [];

        if (str_contains($keywordLower, ' in ')) {
            $keywordArray = explode(" in ", $keyword);
            $fkeyword = !empty($keywordArray) && isset($keywordArray[0]) ? trim($keywordArray[0]) : "";
            $dtpack = !empty($keywordArray) && isset($keywordArray[1]) && is_numeric($keywordArray[1]) ? $keywordArray[1] : "";
        } else {
            $keywordArray = explode(" ", $keyword);
        }


        if (!empty($dtpack) && !empty($fkeyword) && sizeof($keywordArray) == 2) {
            $productsA = Product::select("products.prod_id", "products.ac4 as parent_product_code", "clean_description")->where('pack_size', $dtpack)->where('is_parent', 1)->where('ac4', 'NOT LIKE', '%**%')
                            ->where(function ($query) use ($fkeyword) {
                                $query->where('ac4', 'LIKE', '%' . $fkeyword . '%')
                                ->orWhere('clean_description', 'LIKE', '%' . $fkeyword . '%');
                            })->orderBy("ac4")->get()->toArray();
        } elseif (sizeof($keywordArray) > 1) {
            foreach ($keywordArray as $term) {
                $bits[] = "clean_description LIKE '%" . $term . "%'";
            }
            $key = implode(' AND ', $bits);
            $sqlKey = (!empty($key)) ? trim($key) : '';

            $productsA = DB::select(DB::raw("select prod_id, ac4 as parent_product_code, clean_description from products p  where is_parent = 1 and ac4 not like '%**%' and " . $sqlKey));

            $productsA = json_decode(json_encode($productsA), true);
        } else {


            $productsA = Product::select("products.prod_id", "products.ac4 as parent_product_code", "clean_description", "products.product_desc")
                            ->where('products.is_parent', 1)->where('products.ac4', 'NOT LIKE', '%**%')->where(function ($query1) use ($keyword) {
                        $query1->where('products.ac4', 'LIKE', '%' . $keyword . '%')
                                ->orWhere('products.clean_description', 'LIKE', '%' . $keyword . '%');
                    })->orderBy("ac4")->get()->toArray();
        }

      
        return $productsA;
    }
    /**
     * Gets the product page header details like product code, description and dt_pack
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getProductHeader($productid)
    {
         $ac4 = Product::where("prod_id" , $productid)->pluck('ac4')->first();
       
        $productObj = Product::select("products.ac4 as parent_product_code", "clean_description", "products.dt_pack", "products.pack_size")
                ->where("ac4" , $ac4)->where("ac5" , 'SPOT')->first();
        
        if(empty($productObj)) {
            $productObj = Product::select("products.ac4 as parent_product_code", "clean_description", "products.dt_pack", "products.pack_size")
                ->where("ac4" , $ac4)->where("is_parent" , 1)->first();
        }
        
        $header = !empty($productObj) ? $productObj->toArray() : [];
        //If DT PACK is zero, sets it to "NA"

        $header["dt_pack"] = !empty($header["dt_pack"]) ? $header["dt_pack"] : '';
        if (empty($header["dt_pack"])) {
            $header["dt_pack"] = !empty($header["pack_size"]) ? $header["pack_size"] : "NA";
        }
        return $header;
    }

    /**
     * Gets the Usage data from price capture
     * Data for current month, previous 3 months and average of previous 3 months
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getUsageData($productid)
    {
        $usagedata = [];
        $priceConcession = $currentM = [];
        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();
        //Get last day of previous month
        $lstPrvMon = date('Y-m-d', strtotime('last day of previous month'));

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        //Gets all sources
        $sourcesObj = PricingSource::select("name")->where(["source_type" => PricingSource::USAGE])->where("name", "<>", "Sigma Monthly Usage")->get()->toArray();
        $sources = array_column($sourcesObj, "name");
        //$source = 'Price concession';
        $usage = [];

        foreach ($sources as $source) {
            $currentM = self::getCurrentMonthUsage($productParentCode, $source);
            $m1 = self::getCurrentM1Usage($productParentCode, $source);
            $m2 = self::getCurrentM2Usage($productParentCode, $source);
            $m3 = self::getCurrentM3Usage($productParentCode, $source);

            $avgCnt = 0;
            if (!empty($m1)) {
                $avgCnt = $avgCnt + 1;
            }
            if (!empty($m2)) {
                $avgCnt = $avgCnt + 1;
            }

            if (!empty($m3)) {
                $avgCnt = $avgCnt + 1;
            }
            if (empty($m1) && empty($m2) && empty($m3)) {
                $avgCnt = 1;
            }
            $average = ($m1 + $m2 + $m3) / $avgCnt;
            // $average = is_float($average) ? number_format((float) $average, 2) : $average;
            $source = str_replace('Prescription Cost Analysis (PCA) - Display month', 'PCA', $source);
            if ($source == 'PCA') {
                $usage[] = [
                    "source" => $source,
                    "current_month" => !empty($currentM) ? number_format($currentM) : ' - ',
                ];
            } else {
                $usage[] = [
                    "source" => $source,
                    "current_month" => !empty($currentM) ? number_format($currentM) : ' - ',
                    "m1" => !empty($m1) ? floatval($m1) : ' - ',
                    "m2" => !empty($m2) ? floatval($m2) : ' - ',
                    "m3" => !empty($m3) ? floatval($m3) : ' - ',
                    "average" => !empty($average) ? number_format(floatval($average), 2) : ' '
                ];
            }
        }
        return $usage;
    }

    // Returns the values of the sigma product usage
    public static function getSigmaUsageData($productid)
    {
        $usagedata = [];
        $priceConcession = $currentM = [];
        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();
        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";
        $child_prod = Product::select("products.product_code")->where(["products.ac4" => $productParentCode])->get()->toArray();

        $sig = DB::table('sig_usage')
                ->select('usage_id', 'product_code', 'usage_3', 'usage_2', 'usage_1')
                ->where("sig_usage.depot_code", 1)
                ->whereIn("sig_usage.product_code", $child_prod)
                ->get();

        return $sig;

        $usage = [];

        foreach ($sources as $source) {
            $currentM = self::getCurrentMonthUsage($productParentCode, $source);
            $m1 = self::getCurrentM1Usage($productParentCode, $source);
            $m2 = self::getCurrentM2Usage($productParentCode, $source);
            $m3 = self::getCurrentM3Usage($productParentCode, $source);

            $avgCnt = 0;
            if (!empty($m1)) {
                $avgCnt = $avgCnt + 1;
            }
            if (!empty($m2)) {
                $avgCnt = $avgCnt + 1;
            }

            if (!empty($m3)) {
                $avgCnt = $avgCnt + 1;
            }
            if (empty($m1) && empty($m2) && empty($m3)) {
                $avgCnt = 1;
            }
            $average = ($m1 + $m2 + $m3) / $avgCnt;
            // $average = is_float($average) ? number_format((float) $average, 2) : $average;
            $source = str_replace('Prescription Cost Analysis (PCA) - Display month', 'PCA', $source);
            if ($source == 'PCA') {
                $usage[] = [
                    "source" => $source,
                    "current_month" => !empty($currentM) ? number_format($currentM) : ' - ',
                ];
            } else {
                $usage[] = [
                    "source" => $source,
                    "current_month" => !empty($currentM) ? number_format($currentM) : ' - ',
                    "m1" => !empty($m1) ? floatval($m1) : ' - ',
                    "m2" => !empty($m2) ? floatval($m2) : ' - ',
                    "m3" => !empty($m3) ? floatval($m3) : ' - ',
                    "average" => !empty($average) ? number_format(floatval($average), 2) : ' '
                ];
            }
        }
        return $usage;
    }

    public static function getSigmaUsageDataAddtn($productid)
    {
        $usagedata = [];
        $priceConcession = $currentM = [];
        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        $child_prod = Product::select("products.product_code")->where(["products.ac4" => $productParentCode])->get()->toArray();

        $sig = DB::table('sig_usage')
                ->select('usage_id', 'product_code', 'usage_3', 'usage_2', 'usage_1')
                ->where("sig_usage.depot_code", 1)
                ->whereIn("sig_usage.product_code", $child_prod)
                ->get();

        $i = 3;
        while ($i > 0) {
            $usage = 'usage_' . $i;
            $usage_addtn[$usage] = $sig->sum($usage);
            $i--;
        }
        return $usage_addtn;
    }

    public static function getSigmaUsagePercent($productid)
    {
        $usagedata = [];
        $priceConcession = $currentM = [];
        $productParentCode = Product::where(["prod_id" => $productid])->pluck("products.ac4")->first();

        $child_prod = Product::select("products.product_code")->where(["products.ac4" => $productParentCode])->get()->toArray();

        $sig = DB::table('sig_usage')
                ->select('usage_id', 'product_code', 'usage_3', 'usage_2', 'usage_1')
                ->where("sig_usage.depot_code", 1)
                ->whereIn("sig_usage.product_code", $child_prod)
                ->get();

        $i = 3;
        while ($i > 0) {
            $usage = 'usage_' . $i;
            $usage_addtn[$usage] = $sig->sum($usage);
            $i--;
        }

        $currentM = self::getLatestPCA($productid);
        //$use_percentage[] = [];
        $use_percentage["percentage_3"] = !empty($usage_addtn["usage_3"]) && isset($usage_addtn["usage_3"]) ? sprintf('%.2f', ($usage_addtn["usage_3"] / $currentM) * 100) : 0;
        $use_percentage["percentage_2"] = !empty($usage_addtn["usage_2"]) && isset($usage_addtn["usage_2"]) ? sprintf('%.2f', ($usage_addtn["usage_2"] / $currentM) * 100) : 0;
        $use_percentage["percentage_1"] = !empty($usage_addtn["usage_1"]) && isset($usage_addtn["usage_1"]) ? sprintf('%.2f', ($usage_addtn["usage_1"] / $currentM) * 100) : 0;

        return $use_percentage;
    }

    public static function getSigmaSalesVolumeData($productid)
    {

        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();
        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        $salesVolumeData = DB::select("
   SELECT
        MAX(volume_id) as usage_id,
        ProductCode as product_code,
          MAX(SupplierCode) as supplier_code,
          SUM(CASE WHEN MONTH(InvoiceDate) = FORMAT(DATEADD(MONTH, -2, GETDATE()), 'MM') AND YEAR(InvoiceDate) = YEAR(DATEADD(year, -1, GETDATE()))  THEN Volume ELSE 0 END) AS usage_3,
                SUM(CASE WHEN MONTH(InvoiceDate) = FORMAT(DATEADD(MONTH, -1, GETDATE()), 'MM') AND YEAR(InvoiceDate) = YEAR(GETDATE()) THEN Volume ELSE 0 END) AS usage_2,

        SUM(CASE WHEN MONTH(InvoiceDate) = MONTH(GETDATE()) AND YEAR(InvoiceDate) = YEAR(GETDATE()) THEN Volume ELSE 0 END) AS usage_1

    FROM 
        sales_volume
    WHERE 
        InvoiceDate >= DATEADD(MONTH, -3, GETDATE())
        and AC4 = '" . $productParentCode . "'
-- Filter for last 3 months data
    GROUP BY 
        ProductCode"
        );
        return $salesVolumeData;
    }

    public static function getSigmaSalesVolumeDataSum($productid)
    {

        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        $salesVolumeSum = DB::select("
    SELECT 

                       
                    CAST(SUM(CASE WHEN MONTH(InvoiceDate) = FORMAT(DATEADD(MONTH, -2, GETDATE()), 'MM') AND YEAR(InvoiceDate) = YEAR(DATEADD(year, -1, GETDATE())) THEN Volume ELSE 0 END) AS INT)  AS usage_3,                    

CAST(SUM(CASE WHEN MONTH(InvoiceDate) = FORMAT(DATEADD(MONTH, -1, GETDATE()), 'MM') AND YEAR(InvoiceDate) = YEAR(GETDATE()) THEN Volume ELSE 0 END) AS INT)  AS usage_2,
        SUM(CASE WHEN MONTH(InvoiceDate) = MONTH(GETDATE()) AND YEAR(InvoiceDate) = YEAR(GETDATE()) THEN Volume ELSE 0 END) AS usage_1
    FROM 
    sales_volume
WHERE 
    InvoiceDate >= DATEADD(MONTH, -3, GETDATE())
    AND AC4 = '" . $productParentCode . "'"
        );
        $salesVolumeSum = json_decode(json_encode($salesVolumeSum), true);

        $salesVolumeSum1 = !empty($salesVolumeSum[0]) && isset($salesVolumeSum[0]) ? array_map('intval', $salesVolumeSum[0]) : [];
        return $salesVolumeSum1;
    }

    public static function getSalesVolumneDataPercent($productid)
    {
//        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();
//
//        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";
//
//        $salesVolumeSumP = DB::select("
//    SELECT  
//    CAST(CAST(SUM(CASE WHEN InvoiceDate >= DATEADD(MONTH, -3, GETDATE()) AND InvoiceDate < DATEADD(MONTH, -2, GETDATE())  THEN Volume ELSE 0 END) AS DECIMAL) / NULLIF(SUM(Volume), 0) * 100 AS DECIMAL(10,2)) AS percentage_3,
//    CAST(CAST(SUM(CASE WHEN InvoiceDate >= DATEADD(MONTH, -2, GETDATE()) AND InvoiceDate < DATEADD(MONTH, -1, GETDATE()) THEN Volume ELSE 0 END) AS DECIMAL) / NULLIF(SUM(Volume), 0) * 100 AS DECIMAL(10,2)) AS percentage_2,    
//CAST(CAST(SUM(CASE WHEN MONTH(InvoiceDate) = MONTH(GETDATE()) AND YEAR(InvoiceDate) = YEAR(GETDATE()) THEN Volume ELSE 0 END) AS DECIMAL) / NULLIF(SUM(Volume), 0) * 100 AS DECIMAL(10,2)) AS percentage_1
//
//   FROM 
//    sales_volume
//WHERE 
//    InvoiceDate >= DATEADD(MONTH, -3, GETDATE())
//    AND AC4 = '" . $productParentCode . "'"
//        );
//        $salesVolumeSumPer = !empty($salesVolumeSumP[0]) && isset($salesVolumeSumP[0]) ? $salesVolumeSumP[0] : [];

        $usage_addtn = self::getSigmaSalesVolumeDataSum($productid);

//       $total = array_sum($usage_addtn);

        $currentM = self::getLatestPCA($productid);
        //$use_percentage[] = [];
        $use_percentage["percentage_3"] = !empty($usage_addtn["usage_3"]) && isset($usage_addtn["usage_3"]) ? sprintf('%.2f', ($usage_addtn["usage_3"] / $currentM) * 100) : 0;
        $use_percentage["percentage_2"] = !empty($usage_addtn["usage_2"]) && isset($usage_addtn["usage_2"]) ? sprintf('%.2f', ($usage_addtn["usage_2"] / $currentM) * 100) : 0;
        $use_percentage["percentage_1"] = !empty($usage_addtn["usage_1"]) && isset($usage_addtn["usage_1"]) ? sprintf('%.2f', ($usage_addtn["usage_1"] / $currentM) * 100) : 0;

        return $use_percentage;
    }

    public static function getLatestPCA($productid)
    {
        $currentM = 0;
        $productParentCode = Product::where(["prod_id" => $productid])->pluck("products.ac4")->first();

        $currentM = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.id" => 3])->OrderBy("volume_from_date", "DESC")->pluck("volume")->first();
        return $currentM;
    }

    private static function getCurrentMonthUsage($productParentCode, $source)
    {

        //Get usage of current Month for source
        $currentMonth = date('m');

        /* if ($source == 'Price Concession') {
          $currentM = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $source])->whereRaw('MONTH(price_from_date) = ?', [$currentMonth])->OrderBy("price_from_date", "DESC")->pluck("price")->first();
          } else {

          $currentM = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])->whereRaw('MONTH(volume_from_date) = ?', [$currentMonth])->OrderBy("volume_from_date", "DESC")->pluck("volume")->first();
          } */
        $currentM = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])->whereRaw('MONTH(volume_from_date) = ?', [$currentMonth])->OrderBy("volume_from_date", "DESC")->pluck("volume")->first();
        return $currentM;
    }

    private static function getCurrentM1Usage($productParentCode, $source)
    {
        //Get usage of prevoius month
        //$tillDate = \Carbon\Carbon::now()->startOfMonth()->subMonth(1);
        //$fromDate = \Carbon\Carbon::now()->startOfMonth();
        $tillDate = date('Y-m-d', strtotime('last day of last month'));
        $fromDate = date('Y-m-d', strtotime('first day of last month'));

        /* if ($source == 'Price Concession') {
          $m1 = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $source])->whereBetween('price_from_date', [$fromDate, $tillDate])->OrderBy("price_from_date", "DESC")->pluck("price")->first();
          } else {
          $m1 = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])
          ->whereBetween('volume_from_date', [$fromDate, $tillDate])->OrderBy("volume_from_date", "DESC")->limit(1)->pluck("volume")->first();
          } */

        $m1 = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])
                        ->whereBetween('volume_from_date', [$fromDate, $tillDate])->OrderBy("volume_from_date", "DESC")->limit(1)->pluck("volume")->first();
        return $m1;
    }

    private static function getCurrentM2Usage($productParentCode, $source)
    {
        //Get usage of 2 months previous
        //$tillDate = \Carbon\Carbon::now()->startOfMonth()->subMonth(2);
        //$fromDate = \Carbon\Carbon::now()->startOfMonth()->subMonth(1);

        $tillDate = date('Y-m-d', strtotime('last day of -2 month'));
        $fromDate = date('Y-m-d', strtotime('first day of -2 month'));

        /* if ($source == 'Price Concession') {
          $m2 = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $source])->whereBetween('price_from_date', [$fromDate, $tillDate])->OrderBy("price_from_date", "DESC")->pluck("price")->first();
          } else {
          $m2 = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])
          ->whereBetween('volume_from_date', [$fromDate, $tillDate])->OrderBy("volume_from_date", "DESC")->limit(1)->pluck("volume")->first();
          } */

        $m2 = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])
                        ->whereBetween('volume_from_date', [$fromDate, $tillDate])->OrderBy("volume_from_date", "DESC")->limit(1)->pluck("volume")->first();
        return $m2;
    }

    private static function getCurrentM3Usage($productParentCode, $source)
    {
        //Get usage of 3 months previous
        //$tillDate = \Carbon\Carbon::now()->startOfMonth()->subMonth(3);
        //$fromDate = \Carbon\Carbon::now()->startOfMonth()->subMonth(2);

        $tillDate = date('Y-m-d', strtotime('last day of -3 month'));
        $fromDate = date('Y-m-d', strtotime('first day of -3 month'));

        /* if ($source == 'Price Concession') {
          $m3 = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $source])->whereBetween('price_from_date', [$fromDate, $tillDate])->OrderBy("price_from_date", "DESC")->pluck("price")->first();
          } else {
          $m3 = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])
          ->whereBetween('volume_from_date', [$fromDate, $tillDate])->OrderBy("volume_from_date", "DESC")->limit(1)->pluck("volume")->first();
          } */

        $m3 = PricingUsageData::join('sources', 'sources.id', '=', 'usage_data.source_id')->where(["usage_data.parent_product_code" => $productParentCode, "sources.name" => $source])
                        ->whereBetween('volume_from_date', [$fromDate, $tillDate])->OrderBy("volume_from_date", "DESC")->limit(1)->pluck("volume")->first();
        return $m3;
    }

    /**
     * Gets the product details like product parent code. description
     * Price capture details about usage and prices provided by different suppliers, customers
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPriceCaptureDataOld($productid)
    {


        $date = date("Y-m-d");
        //Get last day of previous month
        $lstPrvMon = date('Y-m-d', strtotime('last day of previous month'));
        $prevTwoMon = date('Y-m-d', strtotime('-2 month'));
        $prevoneMon = date('Y-m-d', strtotime('-1 month'));
        $prev_three_moth_date = date("Y-m-d", strtotime('first day of -3 month', strtotime($prevoneMon)));
        $pricingdata = $current_historical = $pricing_current_data = $pricing_historical_data = [];

        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        //Price Capture Pricing data
        //Gets all sources
        $sources = PricingSource::select("id", "name")->where(["source_type" => PricingSource::PRICING])->whereNotIn("id", [11])->orderBy("internal_sort")->get()->toArray();

        foreach ($sources as $source) {
            $sourceName = !empty($source) && isset($source['name']) ? trim($source['name']) : '';
            $sourceId = !empty($source) && isset($source['id']) ? trim($source['id']) : '';
            if ($sourceName === 'Drug Tarrif (DT) Prices' || $sourceName === 'Wavedata') {
                /* $pricingdata[] = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')->select(DB::raw("$productid AS productid"), "pricing_data.source_id as sourceid", "sources.name as source", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast")->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])->orderBy("price_untill_date", "DESC")
                  ->first(); */

                switch ($sourceName) {
                    case "Drug Tarrif (DT) Prices":
                        $shortName = "dt";
                        break;
                    case "Wavedata":
                        $shortName = "wavedata";
                        break;

                    default:
                        $shortName = "";
                }

                $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->select(DB::raw("$productid AS productid"), "pricing_data.source_id as sourceid", "sources.name as source", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "price_from_date", "price_untill_date")
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                        ->where("price_untill_date", ">", $prevoneMon)->orderBy("price_untill_date", "DESC")
                        ->get(); //pricing current data price untill date is greater than todays date 
                //Gets the supllier pricing data for prevous 3 months
                $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->select(DB::raw("$productid AS productid"), "pricing_data.source_id as sourceid", "sources.name as source", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "price_from_date", "price_untill_date")
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                        ->where("price_untill_date", ">", $prev_three_moth_date)->where("price_untill_date", "<", $prevoneMon)->orderBy("price_untill_date", "DESC")
                        //->whereBetween("price_untill_date", [$prev_three_moth_date, $lstPrvMon])->orderBy("price_untill_date", "DESC")
                        ->get();

                $current_historical = ["currrent" => $pricing_current_data, "historical" => $pricing_historical_data];

                $sp = ["source" => $shortName, "source_id" => $sourceId, $current_historical, "min_val" => Product::getCheapestPriceNSupplier($pricing_current_data)];

                $pricingdata[] = $sp;

                if (empty($pricingdata) || $pricingdata['0'] == null) {
                    $pricingdata[] = [
                        "productid" => $productid,
                        "sourceid" => $sourceId,
                        "source" => $sourceName,
                        "price" => "",
                        "forecast" => null
                    ];
                }
            } else {
                switch ($sourceName) {
                    case "Supplier Pricing":
                        $shortName = "sp";
                        break;
                    case "Price Concession":
                        $shortName = "pc";
                        break;
                    case "Competitor Pricing":
                        $shortName = "competitor_pricing";
                        break;
//                    case "Independent Retail Pharmacy (IRP) Group / Head Office Pricing":
//                        $shortName = "irp";
//                        break;
                    case "IRP / Buying Group Tender pricing":
                        $shortName = "irpbg";
                        break;
                    case "IRP Day-to-day Pricing / Offers":
                        $shortName = "irpdp";
                        break;
                    case "Telesales":
                        $shortName = "telesales";
                        break;
                    default:
                        $shortName = "";
                }

                //Supplier Pricing
                $previousPDate = date('Y-m-d', strtotime($prevoneMon . ' -1 months'));
                if ($sourceName == 'Price Concession') {
                    $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                            ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id", "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                            ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                            ->where("price_untill_date", ">", $prevoneMon)->orderBy("price_untill_date", "DESC")
                            ->get();
                } elseif ($sourceName == 'Competitor Pricing') {

                    $pricing_current_data = DB::table('dbo.competitor_prices as cp')
                            ->select(DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
                                    DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
                                    DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
                                    DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
                                    DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                                    'product_id',
                                    'phoenix_outofstock',
                                    'trident_outofstock',
                                    'aah_outofstock',
                                    'colorama_outofstock',
                                    'bestway_outofstock',
                                    'AsOfDate'
                            )->whereIn('cp.product_id', $productIds)->orderBy("AsOfDate", "DESC")->limit(1)
                            ->get()
                            ->map(function ($pricing_current_data) {
                        $productCode = DB::table('dbo.DwProduct as dp')
                                ->where("dp.product_id", $pricing_current_data->product_id)->pluck("dp.Product_Code")->first();
                        $pricing_current_data->product_code = $productCode;
                        $pricing_current_data->sensitivity = 'No';
                        if (($pricing_current_data->phoenix_outofstock + $pricing_current_data->trident_outofstock + $pricing_current_data->aah_outofstock + $pricing_current_data->colorama_outofstock + $pricing_current_data->bestway_outofstock) > 1) {
                            $pricing_current_data->sensitivity = 'Yes';
                        }

                        return $pricing_current_data;
                    });
                } elseif ($sourceName == 'Telesales') {

                    $pricing_current_data = DB::table('dbo.telesales_pricing as ts')
                            ->join('DwProduct as p', 'p.Product_Id', '=', 'ts.product_id')
                            ->join('competitors as c', 'c.competitor_id', '=', 'ts.competitor_id')
                            ->select('p.Product_Code as product_code',
                                    'c.name as competitor_name',
                                    DB::raw('CAST(ts.price AS DECIMAL(10,2)) AS price'),
                                    'ts.asofdate as date'
                            )->whereIn('ts.product_id', $productIds)->orderBy("ts.asofdate", "DESC")->limit(10)
                            ->get();
                } else {
                    $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                            ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                            ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id", "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                            ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                            ->where("price_untill_date", ">", $prevoneMon)->orderBy("price_untill_date", "DESC")
                            ->get(); //pricing current data price untill date is greater than todays date 
                }

                if (empty($pricing_current_data) || count($pricing_current_data) < 1) {
                    $prevoneMon = $previousPDate;
                    $prev_three_moth_date = date('Y-m-d', strtotime($prev_three_moth_date . ' -1 months'));
                    $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                            ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                            ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id", "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                            ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                            ->where("price_untill_date", ">", $prevoneMon)->orderBy("price_untill_date", "DESC")
                            ->get();
                }

                $pricing = [];
                $trend = "";
                if ($shortName == 'sp') {
                    foreach ($pricing_current_data as $pcdata) {
                        $productCode = !empty($pcdata["product_code"]) && isset($pcdata["product_code"]) ? $pcdata["product_code"] : '';

                        $source = !empty($pcdata["sourceid"]) && isset($pcdata["sourceid"]) ? $pcdata["sourceid"] : '';
                        $price = !empty($pcdata["price"]) && isset($pcdata["price"]) ? $pcdata["price"] : '';
                        $supplierid = !empty($pcdata["supplierid"]) && isset($pcdata["supplierid"]) ? $pcdata["supplierid"] : '';
                        $fromDate = !empty($pcdata["price_from_date"]) && isset($pcdata["price_from_date"]) ? $pcdata["price_from_date"] : '';
                        //$previousDate = date('Y-m-d', strtotime($fromDate. ' -1 months'));

                        $prevoiusPrice = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.product_code" => $productCode, "pricing_data.source_id" => $source, "pricing_data.supplier_id" => $supplierid])
                                        ->where("price_from_date", "<", $fromDate)->orderBy("price_from_date", "DESC")->limit(1)
                                        ->pluck("pricing_data.price")->first();

                        if ($price > $prevoiusPrice) {
                            $trend = "up";
                        } else if ($price < $prevoiusPrice) {
                            $trend = "down";
                        } else if ($price == $prevoiusPrice) {
                            $trend = "same";
                        } else {
                            $trend = "-";
                        }
                        $pricing[] = [
                            "ac4" => $productParentCode,
                            "productid" => $pcdata["productid"],
                            "pricing_id" => $pcdata["pricing_id"],
                            "supplierid" => $pcdata["supplierid"],
                            "sourceid" => $pcdata["sourceid"],
                            "source" => $pcdata["source"],
                            "supp_code" => $pcdata["supp_code"],
                            "product_code" => $pcdata["product_code"],
                            "previous_price" => number_format($prevoiusPrice, 2),
                            "price" => $pcdata["price"],
                            "pricing_trend" => $trend,
                            "negotiated_price" => $pcdata["negotiated_price"],
                            "forecast" => $pcdata["forecast"],
                            "price_from_date" => $pcdata["price_from_date"],
                            "price_untill_date" => $pcdata["price_untill_date"],
                            "comments" => $pcdata["comments"]];
                    }
                    $pricing_current_data = $pricing;
                }
                if ($sourceName == 'Price Concession') {
                    $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                            ->select(DB::raw("$productid AS productid"), "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                            ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])->where("price_untill_date", ">", $prev_three_moth_date)->where("price_untill_date", "<", $prevoneMon)->orderBy("price_untill_date", "DESC")
                            ->get();
                } elseif ($sourceName == 'Competitor Pricing') {

                    $pricing_historical_data = DB::table('dbo.competitor_prices as cp')
                            ->select(DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
                                    DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
                                    DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
                                    DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
                                    DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                                    'product_id',
                                    'phoenix_outofstock',
                                    'trident_outofstock',
                                    'aah_outofstock',
                                    'colorama_outofstock',
                                    'bestway_outofstock',
                                    'AsOfDate'
                            )->whereIn('cp.product_id', $productIds)->orderBy("AsOfDate", "DESC")
                            ->get()
                            ->map(function ($pricing_historical_data) {
                        $productCode = DB::table('dbo.DwProduct as dp')
                                ->where("dp.product_id", $pricing_historical_data->product_id)->pluck("dp.Product_Code")->first();
                        $pricing_historical_data->product_code = $productCode;
                        $pricing_historical_data->sensitivity = 'No';
                        if (($pricing_historical_data->phoenix_outofstock + $pricing_historical_data->trident_outofstock + $pricing_historical_data->aah_outofstock + $pricing_historical_data->colorama_outofstock + $pricing_historical_data->bestway_outofstock) > 1) {
                            $pricing_historical_data->sensitivity = 'Yes';
                        }

                        return $pricing_historical_data;
                    });
                } elseif ($sourceName == 'Telesales') {

                    $pricing_historical_data = DB::table('dbo.telesales_pricing as ts')
                            ->join('DwProduct as p', 'p.Product_Id', '=', 'ts.product_id')
                            ->join('competitors as c', 'c.competitor_id', '=', 'ts.competitor_id')
                            ->select('p.Product_Code as product_code',
                                    'c.name as competitor_name',
                                    DB::raw('CAST(ts.price AS DECIMAL(10,2)) AS price'),
                                    'ts.asofdate as date'
                            )->whereIn('ts.product_id', $productIds)->orderBy("ts.asofdate", "DESC")->limit(10)
                            ->get();
                } else {
                    //Gets the supllier pricing data for prevous 3 months
                    $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                            ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                            ->select(DB::raw("$productid AS productid"), "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                            ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])->where("price_untill_date", ">", $prev_three_moth_date)->where("price_untill_date", "<", $prevoneMon)->orderBy("price_untill_date", "DESC")
                            ->get();
                }
                $current_historical = ["currrent" => $pricing_current_data, "historical" => $pricing_historical_data];

                $sp = ["source" => $shortName, "source_id" => $sourceId, $current_historical, "min_val" => Product::getCheapestPriceNSupplier($pricing_current_data)];

                $pricingdata[] = $sp;
            }
        }


        return $pricingdata;
    }

    /**
     * Gets the product details like product parent code. description
     * Price capture details about usage and prices provided by different suppliers, customers
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPriceCaptureData($productid, $type, $page, $sortcolumn, $sort)
    {

        switch ($type) {
            case 1:
                return self::getSupplierPricingData($productid, $page, $sortcolumn, $sort);
                break;
            case 2:
                return self::getDTData($productid, $page, $sortcolumn, $sort);
                break;
            case 3:
                return self::getConcessionData($productid, $page, $sortcolumn, $sort);
                break;
            case 4:
                return self::getWaveData($productid, $page, $sortcolumn, $sort);
                break;
            case 5:
                return self::getCompetitorPriceData($productid, $page, $sortcolumn, $sort);
                break;
            case 6:
                return self::getTelesalesData($productid, $page, $sortcolumn, $sort);
                break;

            case 7:
                return self::getCompetitorOffers($productid, $page, $sortcolumn, $sort);
                break;

            default:
                break;
        }
    }

    /**
     * Gets pricing details from Supplier Pricing source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getSupplierPricingData($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'price_from_date';
            $sorder = "DESC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;
        $pricing = [];
        $trend = "";
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        //Price Capture Pricing data
        $sourceName = 'Supplier Pricing';
        $sourceId = 10;

        $max = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])->max('price_from_date');
        $previousPDate = date('Y-m-d', strtotime($max . ' -12 months'));
        $maxd = PricingSupplierPriceData::selectRaw("MAX(FORMAT(price_from_date, 'yyyy-MM')) as max_year_month")->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])->value('max_year_month');
 
        if (!empty($maxd)) {
            $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id",
                            "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid",
                            "sources.name as source", "suppliers.code as supp_code", "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                    
                            DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '0.00', '0'),'00','')  AS negotiated_price"),
                            "forecast", "price_from_date", "price_untill_date", "pricing_data.comments", \DB::raw("(CASE 
                        WHEN import_type = '1' THEN 'Form Input'
                        WHEN import_type = '2' THEN 'Supplier File' 
                        ELSE 'Manual' 
                       END) AS datasource"))
                    
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') = ?", [$maxd])
                  
//                       ->orderBy("import_type", "DESC")
                     ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(); //pricing current data price untill date is greater than todays date 

            $currendataCnt = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') = ?", [$maxd])
                    ->count();

            $currentallData = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id",
                            "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid",
                            "sources.name as source", "suppliers.code as supp_code", "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '0.00', '0'),'00','')  AS negotiated_price"),
                            "forecast", "price_from_date", "price_untill_date", "pricing_data.comments",
                           )
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') = ?", [$maxd])
                    ->get();
            $filtered = [];
            foreach ($pricing_current_data as $pcdata) {
                $productCode = !empty($pcdata["product_code"]) && isset($pcdata["product_code"]) ? $pcdata["product_code"] : '';

                $source = !empty($pcdata["sourceid"]) && isset($pcdata["sourceid"]) ? $pcdata["sourceid"] : '';
                $price = !empty($pcdata["price"]) && isset($pcdata["price"]) ? $pcdata["price"] : '';
                $supplierid = !empty($pcdata["supplierid"]) && isset($pcdata["supplierid"]) ? $pcdata["supplierid"] : '';
                $fromDate = !empty($pcdata["price_from_date"]) && isset($pcdata["price_from_date"]) ? $pcdata["price_from_date"] : '';
                //$previousDate = date('Y-m-d', strtotime($fromDate. ' -1 months'));

                $prevoiusPrice = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.product_code" => $productCode, "pricing_data.source_id" => $source, "pricing_data.supplier_id" => $supplierid])
                                ->where("price_from_date", "<", $fromDate)->orderBy("price_from_date", "DESC")->limit(1)
                                ->pluck("pricing_data.price")->first();

                if ($price > $prevoiusPrice) {
                    $trend = "up";
                } else if ($price < $prevoiusPrice) {
                    $trend = "down";
                } else if ($price == $prevoiusPrice) {
                    $trend = "same";
                } else {
                    $trend = "-";
                }
//                $pricing[] = [
//                    "ac4" => $productParentCode,
//                    "productid" => $pcdata["productid"],
//                    "pricing_id" => $pcdata["pricing_id"],
//                    "supplierid" => $pcdata["supplierid"],
//                    "sourceid" => $pcdata["sourceid"],
//                    "source" => $pcdata["source"],
//                    "supp_code" => $pcdata["supp_code"],
//                    "product_code" => $pcdata["product_code"],
//                    "previous_price" => number_format($prevoiusPrice, 2),
//                    "price" => $pcdata["price"],
//                    "pricing_trend" => $trend,
//                    "negotiated_price" => $pcdata["negotiated_price"],
//                    "forecast" => $pcdata["forecast"],
//                    "price_from_date" => $pcdata["price_from_date"],
//                    "price_untill_date" => $pcdata["price_untill_date"],
//                    "comments" => $pcdata["comments"]];
                
                 $pricing[$pcdata["supp_code"]][$pcdata["import_type"]][$pcdata["price_from_date"]] = [
                    "ac4" => $productParentCode,
                    "productid" => $pcdata["productid"],
                    "pricing_id" => $pcdata["pricing_id"],
                    "supplierid" => $pcdata["supplierid"],
                    "sourceid" => $pcdata["sourceid"],
                    "source" => $pcdata["source"],
                    "supp_code" => $pcdata["supp_code"],
                    "product_code" => $pcdata["product_code"],
                    "previous_price" => number_format($prevoiusPrice, 2),
                    "price" => $pcdata["price"],
                    "pricing_trend" => $trend,
                    "negotiated_price" => $pcdata["negotiated_price"],
                    "forecast" => $pcdata["forecast"],
                    "price_from_date" => $pcdata["price_from_date"],
                    "price_untill_date" => $pcdata["price_untill_date"],
                    "comments" => $pcdata["comments"],
                    "datasource" => $pcdata["datasource"]];
                
            }

            foreach ($pricing as $value) {
            
                $so = reset($value);
                krsort($so);
              
              $filtered[] = reset($so);  
            }
            $pricing_current_data = $filtered;
//            $pricing_current_data = ["0" => reset($pricing_current_data)];
             
            //Gets the supllier pricing data for prevous 3 months
            $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id", "pricing_data.supplier_id as supplierid",
                            "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code",
                            "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '0.00', '0'),'00','')  AS negotiated_price"),
                            "forecast", "price_from_date", "price_untill_date", "pricing_data.comments",
                             \DB::raw("(CASE 
                        WHEN import_type = '1' THEN 'Form Input'
                        WHEN import_type = '2' THEN 'Supplier File' 
                        ELSE 'Manual' 
                       END) AS datasource"))
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') != ?", [$maxd])
                    ->where("price_from_date", ">=", $previousPDate)
                    ->where("price_from_date", "<", $max)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') != ?", [$maxd])
                    ->where("price_from_date", ">=", $previousPDate)
                    ->where("price_from_date", "<", $max)
                    ->count();
        }

        $pricingdata = ["source" => 'sp', "source_id" => $sourceId, "currrent" => $pricing_current_data, "historical" => $pricing_historical_data, "min_val" => Product::getCheapestPriceNSupplier($currentallData),
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];

        return $pricingdata;
    }

    /**
     * Gets pricing details from Telesales source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getTelesalesData($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'asofdate';
            $sorder = "DESC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        $max = DB::table('dbo.telesales_pricing as ts')->whereIn('product_id', $productIds)->max('asofdate');
        $previousPDate = date('Y-m-d', strtotime($max . ' -12 months'));
        $maxd = DB::table('dbo.telesales_pricing as ts')->selectRaw("MAX(FORMAT(asofdate, 'yyyy-MM')) as max_year_month")->whereIn('product_id', $productIds)->value('max_year_month');

        $sourceId = 17;

        if (!empty($maxd)) {

            $pricing_current_data = DB::table('dbo.telesales_pricing as ts')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'ts.product_id')
                    ->join('competitors as c', 'c.competitor_id', '=', 'ts.competitor_id')
                    ->select('p.Product_Code as product_code',
                            'c.name as competitor_name',
                              DB::raw("REPLACE(REPLACE(CAST(ts.price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            'ts.asofdate as date'
                    )->whereIn('ts.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') = ?", [$maxd])
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(); //pricing current data price untill date is greater than todays date 


            $currendataCnt = DB::table('dbo.telesales_pricing as ts')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'ts.product_id')
                    ->join('competitors as c', 'c.competitor_id', '=', 'ts.competitor_id')
                    ->whereIn('ts.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') = ?", [$maxd])
                    ->count();

            $pricing_historical_data = DB::table('dbo.telesales_pricing as ts')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'ts.product_id')
                    ->join('competitors as c', 'c.competitor_id', '=', 'ts.competitor_id')
                    ->select('p.Product_Code as product_code',
                            'c.name as competitor_name',
                             DB::raw("REPLACE(REPLACE(CAST(ts.price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            'ts.asofdate as date'
                    )->whereIn('ts.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') != ?", [$maxd])
                    ->where("asofdate", ">=", $previousPDate)
                    ->where("asofdate", "<", $max)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = DB::table('dbo.telesales_pricing as ts')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'ts.product_id')
                    ->join('competitors as c', 'c.competitor_id', '=', 'ts.competitor_id')
                    ->whereIn('ts.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') != ?", [$maxd])
                    ->where("asofdate", ">=", $previousPDate)
                    ->where("asofdate", "<", $max)
                    ->count();
        }

        $pricingdata = ["source" => 'ts', "source_id" => $sourceId,
            "currrent" => $pricing_current_data, "historical" => $pricing_historical_data,
            'spots' => ProductBackground::getSpots($productid), 'competitors' => Product::getCompetitors(),
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];
        return $pricingdata;
    }

    /**
     * Gets pricing details from Competitor Pricing source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getCompetitorPriceData($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'AsOfDate';
            $sorder = "DESC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';

        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        $max = DB::table('dbo.competitor_prices')->whereIn('product_id', $productIds)->max('AsOfDate');
        $previousPDate = date('Y-m-d', strtotime($max . ' -12 months'));
        $maxd = DB::table('dbo.competitor_prices')->selectRaw("MAX(FORMAT(AsOfDate, 'yyyy-MM')) as max_year_month")->whereIn('product_id', $productIds)->value('max_year_month');

        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;

        $sourceId = 15;
        $latestDates = [];
        $latestDates = DB::table('competitor_prices')
                        ->select(DB::raw("FORMAT(AsOfDate, 'yyyy-MM') AS year_month"))
                        ->whereIn('product_id', $productIds)
                        ->groupBy(DB::raw("FORMAT(AsOfDate, 'yyyy-MM')"))
                        ->orderByDesc('year_month')
                        ->take(14)
                        ->pluck('year_month')->toArray();

        if (!empty($latestDates)) {
            $pricing_current_data = DB::table('dbo.competitor_prices as cp')
                    ->select(DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                            DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                            DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                            DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                            DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
//                            DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
//                            DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
//                            DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
//                            DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
//                            DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                            'product_id',
                            'phoenix_outofstock',
                            'trident_outofstock',
                            'aah_outofstock',
                            'colorama_outofstock',
                            'bestway_outofstock',
                            'AsOfDate'
                    )->whereIn('cp.product_id', $productIds)
                    ->whereRaw("FORMAT(AsOfDate, 'yyyy-MM') IN ('" . implode("','", $latestDates) . "')")
                    ->get()
                    ->map(function ($pricing_current_data) {
                $productCode = DB::table('dbo.DwProduct as dp')
                        ->where("dp.product_id", $pricing_current_data->product_id)->pluck("dp.Product_Code")->first();
                $pricing_current_data->product_code = $productCode;

                return $pricing_current_data;
            });

            $currendataCnt = DB::table('dbo.competitor_prices as cp')
                    ->whereIn('cp.product_id', $productIds)
                    ->whereRaw("FORMAT(AsOfDate, 'yyyy-MM') IN ('" . implode("','", $latestDates) . "')")
                    ->count();

            $pricing_historical_data = DB::table('dbo.competitor_prices as cp')
                    ->select(DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
                            DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
                            DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
                            DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
                            DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                            'product_id',
                            'phoenix_outofstock',
                            'trident_outofstock',
                            'aah_outofstock',
                            'colorama_outofstock',
                            'bestway_outofstock',
                            'AsOfDate'
                    )->whereIn('cp.product_id', $productIds)
                    ->whereRaw("FORMAT(AsOfDate, 'yyyy-MM') NOT IN ('" . implode("','", $latestDates) . "')")
                    ->where("AsOfDate", ">=", $previousPDate)
                    ->where("AsOfDate", "<=", $max)
                    ->get()
                    ->map(function ($pricing_historical_data) {
                $productCode = DB::table('dbo.DwProduct as dp')
                        ->where("dp.product_id", $pricing_historical_data->product_id)->pluck("dp.Product_Code")->first();
                $pricing_historical_data->product_code = $productCode;

                return $pricing_historical_data;
            });

            $historicalCnt = DB::table('dbo.competitor_prices as cp')
                    ->whereIn('cp.product_id', $productIds)
                    ->whereRaw("FORMAT(AsOfDate, 'yyyy-MM') NOT IN ('" . implode("','", $latestDates) . "')")
                    ->where("AsOfDate", ">=", $previousPDate)
                    ->where("AsOfDate", "<=", $max)
                    ->count();
        }
        $pricingdata = ["source" => 'cp', "source_id" => $sourceId,
            "currrent" => $pricing_current_data, "historical" => $pricing_historical_data,
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];
        return $pricingdata;
    }

    /**
     * Gets pricing details from Wavedata source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getWaveData($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;

        if (empty($sortcolumn)) {
            $sortcolumn = 'asofdate';
            $sorder = "DESC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        $max = DB::table('dbo.wavedata')->whereIn('product_id', $productIds)->max('asofdate');
        $previousPDate = date('Y-m-d', strtotime($max . ' -12 months'));
        $maxd = DB::table('dbo.wavedata')->selectRaw("MAX(FORMAT(asofdate, 'yyyy-MM')) as max_year_month")->whereIn('product_id', $productIds)->value('max_year_month');

        $sourceId = 14;
        if (!empty($maxd)) {
            $pricing_current_data = DB::table('dbo.wavedata as w')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'w.product_id')
                    ->select('p.Product_Code as product_code',
                            DB::raw("REPLACE(REPLACE(CAST(cheapest1 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS cheapest1"),
                            'vendor1',
                            'date1',
                            DB::raw("REPLACE(REPLACE(CAST(cheapest2 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS cheapest2"),
                            'vendor2',
                            'date2',
                            'w.asofdate as date'
                    )->whereIn('w.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') = ?", [$maxd])
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(); //pricing current data price untill date is greater than todays date 

            $currendataCnt = DB::table('dbo.wavedata as w')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'w.product_id')
                    ->whereIn('w.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') = ?", [$maxd])
                    ->count();

            $pricing_historical_data = DB::table('dbo.wavedata as w')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'w.product_id')
                    ->select('p.Product_Code as product_code',
                            DB::raw("REPLACE(REPLACE(CAST(cheapest1 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS cheapest1"),
                            'date1',
                            'vendor1',
                            DB::raw("REPLACE(REPLACE(CAST(cheapest2 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS cheapest2"),
                            'date2',
                            'vendor2',
                            'w.asofdate as date'
                    )->whereIn('w.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') != ?", [$maxd])
                    ->where("asofdate", ">=", $previousPDate)
                    ->where("asofdate", "<", $max)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = DB::table('dbo.wavedata as w')
                    ->join('DwProduct as p', 'p.Product_Id', '=', 'w.product_id')
                    ->whereIn('w.product_id', $productIds)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') != ?", [$maxd])
                    ->where("asofdate", ">=", $previousPDate)
                    ->where("asofdate", "<", $max)
                    ->count();
        }
        $pricingdata = ["source" => 'wa', "source_id" => $sourceId,
            "currrent" => $pricing_current_data, "historical" => $pricing_historical_data,
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];
        return $pricingdata;
    }

    /**
     * Gets pricing details from Drug Tarrif (DT) Prices source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getDTData($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;

        if (empty($sortcolumn)) {
            $sortcolumn = 'price_from_date';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }


        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        //Price Capture Pricing data
        $sourceName = 'Drug Tarrif (DT) Prices';
        $sourceId = 2;

        //Supplier Pricing

        $max = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])->max('price_from_date');
        $previousPDate = date('Y-m-d', strtotime($max . ' -48 months'));

        $latestDates = Helper::getLatest3MonthDates($productParentCode, $sourceId);

        if (!empty($latestDates)) {
            $pricing_current_data = PricingSupplierPriceData::select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id",
                            "pricing_data.source_id as sourceid","pricing_data.product_code as spotcode",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            "price_from_date as date")
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') IN ('" . implode("','", $latestDates) . "')")
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(); //pricing current data price untill date is greater than todays date 

            $currendataCnt = PricingSupplierPriceData::
                    where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') IN ('" . implode("','", $latestDates) . "')")
                    ->count();

            //Gets the supllier pricing data for prevous 3 months
            $pricing_historical_data = PricingSupplierPriceData::select(DB::raw("$productid AS productid"),
                            "pricing_data.source_id as sourceid","pricing_data.product_code as spotcode",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            "forecast", "price_from_date as date")
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') NOT IN ('" . implode("','", $latestDates) . "')")
                    ->where("price_from_date", ">=", $previousPDate)
                    ->where("price_from_date", "<=", $max)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') NOT IN ('" . implode("','", $latestDates) . "')")
                    ->where("price_from_date", ">=", $previousPDate)
                    ->where("price_from_date", "<=", $max)
                    ->count();
        }
        $pricingdata = ["source" => 'dt', "source_id" => $sourceId, "currrent" => $pricing_current_data,
            "historical" => $pricing_historical_data,
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];

        return $pricingdata;
    }

    /**
     * Gets pricing details from Price Concession source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getConcessionData($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'price_from_date';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        //Price Capture Pricing data

        $sourceId = 1;

        //Supplier Pricing

        $max = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => 1])->max('price_from_date');
        $previousPDate = date('Y-m-d', strtotime($max . ' -82 months'));

        $latestDates = Helper::getLatest3MonthDates($productParentCode, $sourceId);
        if (!empty($latestDates)) {
            $pricing_current_data = PricingSupplierPriceData::select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id",
                            "pricing_data.source_id as sourceid",
                            "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            "price_from_date as date")
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') IN ('" . implode("','", $latestDates) . "')")
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get(); //pricing current data price untill date is greater than todays date 

            $currendataCnt = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') IN ('" . implode("','", $latestDates) . "')")
                    ->count();
        }

        if (!empty($previousPDate) && !empty($max)) {
            $pricing_historical_data = PricingSupplierPriceData::select(DB::raw("$productid AS productid"),
                            "pricing_data.source_id as sourceid",
                            "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            "forecast", "price_from_date as date")
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') NOT IN ('" . implode("','", $latestDates) . "')")
                    ->where("price_from_date", ">=", $previousPDate)
                    ->where("price_from_date", "<=", $max)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                    ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') NOT IN ('" . implode("','", $latestDates) . "')")
                    ->where("price_from_date", ">=", $previousPDate)
                    ->where("price_from_date", "<=", $max)
                    ->count();
        }
        $pricingdata = ["source" => 'pc', "source_id" => $sourceId, "currrent" => $pricing_current_data,
            "historical" => $pricing_historical_data,
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt];

        return $pricingdata;
    }

    /**
     * Gets pricing details from Competitor Offers source
     *
     * @param  integer $productid The Product ID
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    private static function getCompetitorOffers($productid, $page, $sortcolumn, $sort)
    {

        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'asofdate';
            $sorder = "DESC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        $pricing_current_data = $pricing_historical_data = [];
        $currendataCnt = $historicalCnt = 0;
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';

        $max = DB::table('dbo.competitor_offers')->where('ac4', $productParentCode)->max('asofdate');
        $previousPDate = date('Y-m-d', strtotime($max . ' -12 months'));
        $maxd = DB::table('dbo.competitor_offers')->selectRaw("MAX(FORMAT(asofdate, 'yyyy-MM')) as max_year_month")
                        ->where('ac4', $productParentCode)->value('max_year_month');

        $pricingdata = $current_historical = $pricing_current_data = $pricing_historical_data = [];

        $sourceId = 13;
        if (!empty($maxd)) {

            $pricing_current_data = DB::table('dbo.competitor_offers as co')
                    ->select('co.product',
                            'description',
                            'sales_code',
                             'supplier',
                            DB::raw("REPLACE(REPLACE(CAST(trade_price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trade_price"),
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            'stock',
                            'asofdate as date'
                    )->where('co.ac4', $productParentCode)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') = ?", [$maxd])
                    ->get();

            $currendataCnt = DB::table('dbo.competitor_offers as co')
                    ->where('co.ac4', $productParentCode)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') = ?", [$maxd])
                    ->count();

            $pricing_historical_data = DB::table('dbo.competitor_offers as co')
                    ->select('co.product',
                            'description',
                            'sales_code',
                             'supplier',
                            DB::raw("REPLACE(REPLACE(CAST(trade_price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trade_price"),
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                            'stock',
                            'asofdate as date'
                    )->where('co.ac4', $productParentCode)
                    ->whereRaw("FORMAT(asofdate, 'yyyy-MM') != ?", [$maxd])
                    ->where("asofdate", ">=", $previousPDate)
                    ->where("asofdate", "<", $max)
                    ->get();

            $historicalCnt = DB::table('dbo.competitor_offers as co')
                    ->where('co.ac4', $productParentCode)
                    ->whereRaw("FORMAT(AsOfDate, 'yyyy-MM') != ?", [$maxd])
                    ->where("AsOfDate", ">=", $previousPDate)
                    ->where("AsOfDate", "<", $max)
                    ->count();
        }

        $pricingdata = ["source" => 'co', "source_id" => $sourceId,
            "currrent" => $pricing_current_data, "historical" => $pricing_historical_data,
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];
        return $pricingdata;
    }

    // *********************************************************************************************************************************
    public static function getPriceCaptureData2($productid)
    {


        $date = date("Y-m-d");
        //Get last day of previous month
        $lstPrvMon = date('Y-m-d', strtotime('last day of previous month'));
        $prevTwoMon = date('Y-m-d', strtotime('-2 month'));
        $prevoneMon = date('Y-m-d', strtotime('-1 month'));
        $prev_three_moth_date = date("Y-m-d", strtotime('first day of -3 month', strtotime($prevoneMon)));
        $pricingdata = $current_historical = $pricing_current_data = $pricing_historical_data = [];

        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        //Price Capture Pricing data
        //Gets all sources
        $sources = PricingSource::select("id", "name")->where(["source_type" => PricingSource::PRICING])->where("name", "<>", "Wavedata Report")->where("name", "<>", "Price Concession")->orderBy("internal_sort")->get()->toArray();

        foreach ($sources as $source) {
            $sourceName = !empty($source) && isset($source['name']) ? trim($source['name']) : '';
            $sourceId = !empty($source) && isset($source['id']) ? trim($source['id']) : '';
            if ($sourceName === 'Drug Tarrif (DT) Prices' || $sourceName === 'Wavedata' || $sourceName === 'PHD') {

                switch ($sourceName) {
                    case "Drug Tarrif (DT) Prices":
                        $shortName = "dt";
                        break;
                    case "Wavedata":
                        $shortName = "wavedata";
                        break;
                    case "PHD":
                        $shortName = "phd";
                        break;

                    default:
                        $shortName = "";
                }

                $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->select(DB::raw("$productid AS productid"), "pricing_data.source_id as sourceid", "sources.name as source", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "price_from_date", "price_untill_date")
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                        ->where("price_untill_date", ">", $prevoneMon)->orderBy("price_untill_date", "DESC")
                        ->get(); //pricing current data price untill date is greater than todays date 
                //Gets the supllier pricing data for prevous 3 months
                $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->select(DB::raw("$productid AS productid"), "pricing_data.source_id as sourceid", "sources.name as source", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "price_from_date", "price_untill_date")
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                        ->where("price_untill_date", ">", $prev_three_moth_date)->where("price_untill_date", "<", $prevoneMon)->orderBy("price_untill_date", "DESC")
                        //->whereBetween("price_untill_date", [$prev_three_moth_date, $lstPrvMon])->orderBy("price_untill_date", "DESC")
                        ->get();

                $current_historical = ["currrent" => $pricing_current_data, "historical" => $pricing_historical_data];

                $sp = ["source" => $shortName, "source_id" => $sourceId, $current_historical, "min_val" => Product::getCheapestPriceNSupplier($pricing_current_data)];

                $pricingdata[] = $sp;

                if (empty($pricingdata) || $pricingdata['0'] == null) {
                    $pricingdata[] = [
                        "productid" => $productid,
                        "sourceid" => $sourceId,
                        "source" => $sourceName,
                        "price" => "",
                        "forecast" => null
                    ];
                }
            } else {
                switch ($sourceName) {
                    case "Supplier Pricing":
                        $shortName = "sp";
                        break;
                    case "Independent Retail Pharmacy (IRP) Group / Head Office Pricing":
                        $shortName = "irp";
                        break;
                    case "IRP / Buying Group Tender pricing":
                        $shortName = "irpbg";
                        break;
                    case "IRP Day-to-day Pricing / Offers":
                        $shortName = "irpdp";
                        break;
                    default:
                        $shortName = "";
                }
                //Supplier Pricing
                $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                        ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id", "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                        ->where("price_untill_date", ">", $prevoneMon)->orderBy("price_untill_date", "DESC")
                        ->get(); //pricing current data price untill date is greater than todays date 
                $pricing = [];
                $trend = "";
                if ($shortName == 'sp') {
                    foreach ($pricing_current_data as $pcdata) {
                        $productCode = !empty($pcdata["product_code"]) && isset($pcdata["product_code"]) ? $pcdata["product_code"] : '';

                        $source = !empty($pcdata["sourceid"]) && isset($pcdata["sourceid"]) ? $pcdata["sourceid"] : '';
                        $price = !empty($pcdata["price"]) && isset($pcdata["price"]) ? $pcdata["price"] : '';
                        $supplierid = !empty($pcdata["supplierid"]) && isset($pcdata["supplierid"]) ? $pcdata["supplierid"] : '';
                        $fromDate = !empty($pcdata["price_from_date"]) && isset($pcdata["price_from_date"]) ? $pcdata["price_from_date"] : '';
                        //$previousDate = date('Y-m-d', strtotime($fromDate. ' -1 months'));

                        $prevoiusPrice = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.product_code" => $productCode, "pricing_data.source_id" => $source, "pricing_data.supplier_id" => $supplierid])
                                        ->where("price_from_date", "<", $fromDate)->orderBy("price_from_date", "DESC")->limit(1)
                                        ->pluck("pricing_data.price")->first();

                        if ($price > $prevoiusPrice) {
                            $trend = "up";
                        } else if ($price < $prevoiusPrice) {
                            $trend = "down";
                        } else if ($price == $prevoiusPrice) {
                            $trend = "same";
                        } else {
                            $trend = "-";
                        }
                        $pricing[] = ["productid" => $pcdata["productid"],
                            "pricing_id" => $pcdata["pricing_id"],
                            "supplierid" => $pcdata["supplierid"],
                            "sourceid" => $pcdata["sourceid"],
                            "source" => $pcdata["source"],
                            "supp_code" => $pcdata["supp_code"],
                            "product_code" => $pcdata["product_code"],
                            "previous_price" => $prevoiusPrice,
                            "price" => $pcdata["price"],
                            "pricing_trend" => $trend,
                            "negotiated_price" => $pcdata["negotiated_price"],
                            "forecast" => $pcdata["forecast"],
                            "price_from_date" => $pcdata["price_from_date"],
                            "price_untill_date" => $pcdata["price_untill_date"],
                            "comments" => ""];
                    }
                    $pricing_current_data = $pricing;
                }

                //Gets the supllier pricing data for prevous 3 months
                $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                        ->select(DB::raw("$productid AS productid"), "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), DB::raw('CAST(negotiated_price AS DECIMAL(10,2)) AS negotiated_price'), "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])->where("price_untill_date", ">", $prev_three_moth_date)->where("price_untill_date", "<", $prevoneMon)->orderBy("price_untill_date", "DESC")
                        ->get();

                //$lastDay = date('Y-m-t',strtotime('01-04-2022'));			

                $lowest_pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                        ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                        ->select("pricing_data.supplier_id as supplierid", "suppliers.code as supp_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'))
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "sources.name" => $sourceName])
                        ->where("price_untill_date", ">", $lstPrvMon)
                        ->orderBy("price", "ASC")
                        ->get(); //pricing current data price untill date is greater than todays date 
                //$lowest_pricing_current_data = (array) $lowest_pricing_current_data;
                //return $lowest_pricing_current_data;

                $current_historical = ["currrent" => $pricing_current_data, "historical" => $pricing_historical_data];

                $sp = ["source" => $shortName, "source_id" => $sourceId, $current_historical, "min_val" => Product::getCheapestPriceNSupplier($lowest_pricing_current_data)];

                $pricingdata[] = $sp;
            }
        }

        return $pricingdata;
    }

    /**
     * Gets the product details like product parent code. description
     * Price capture details about usage and prices provided by different suppliers, customers
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPricingUsageSummary($productid)
    {

        $pricingdata = [];

        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        //Price Capture Pricing data
        //Gets all sources
        $sources = PricingSource::select("id", "name")->where(["source_type" => PricingSource::PRICING])->whereIn("name", ["Price Concession", 'Drug Tarrif (DT) Prices'])->orderBy("internal_sort")->get()->toArray();

        $sourcesUsage = PricingSource::select("id", "name")->where(["source_type" => PricingSource::USAGE])->where("name", "<>", "Sigma Monthly Usage")->orderBy("internal_sort")->get()->toArray();

        foreach ($sources as $source) {
            $sourceName = !empty($source) && isset($source['name']) ? trim($source['name']) : '';

            $sourceId = !empty($source) && isset($source['id']) ? trim($source['id']) : '';

            if ($sourceName == 'Drug Tarrif (DT) Prices') {
                $sourceName = 'DT Prices';
            }

            $cheapest = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])->orderBy("price_from_date", "DESC")->orderBy("price", "ASC")->pluck('price')->first();
            $cheapest = !empty($cheapest) ? number_format($cheapest, 2) : '-';
            $sp[] = ["source" => $sourceName, 'cleapest' => $cheapest];
        }
        $pricingdata["pricing"] = $sp;

        foreach ($sourcesUsage as $source) {
            $sourceName = !empty($source) && isset($source['name']) ? trim($source['name']) : '';
            $sourceId = !empty($source) && isset($source['id']) ? trim($source['id']) : '';
            $lowest = PricingUsageData::where(["usage_data.parent_product_code" => $productParentCode, "usage_data.source_id" => $sourceId])->orderBy("volume_from_date", "DESC")->orderBy("volume", "ASC")->pluck('volume')->first();
            $lowest = !empty($lowest) ? (int) $lowest : '-';

            if ($sourceName == 'Prescription Cost Analysis (PCA) - Display month') {
                $sourceName = 'PCA';
            }
            $sourceName = str_replace('Usage', '', $sourceName);
            $us[] = ["source" => $sourceName, 'lowest' => $lowest];
        }
        $pricingdata["usage"] = $us;

        return $pricingdata;
    }

    /**
     * Gets the inventory details
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getInventoryDataOld($productid)
    {

        $physical_Stock = $allocation_Stock = $free_stock = $allocation_After = $on_Order = $true_Cost = $std_Cost = 0;
        $inventory = [];
        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();
        //Get last day of previous month
        $lstPrvMon = date('Y-m-d', strtotime('last day of previous month'));

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        //Dataware house Inventory data

        /* $inventory = DwInventory::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=','staging.dw_Inventory.Product_Id')->select("dw_Product.Product_Code", "Physical_Stock", "Allocation_Stock", "Allocation_After", DB::raw("Physical_Stock - Allocation_Stock as Free_stock"), "On_Order", "True_Cost", "Std_Cost", DB::raw("REPLACE(LG_Date, '1900-01-01', '-') as LG_Date"))->where("dw_Product.Product_AC_4", $productParentCode)->where("LG_Date", ">", $lstPrvMon)->orderBy("LG_Date", "DESC")->get(); 
         */
        $inventory = DwInventory::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=', 'staging.dw_Inventory.Product_Id')->select("dw_Product.Product_Code", "dw_Product.Product_AC_5 as supplier", "Depot_Id", "Physical_Stock", "Allocation_Stock", "Allocation_After", DB::raw("Physical_Stock - Allocation_Stock as Free_stock"), "On_Order", "True_Cost", "Std_Cost", "Avg_Cost", DB::raw("REPLACE(LG_Date, '1900-01-01', '-') as LG_Date"), DB::raw("(CASE 

                        WHEN True_Cost = Std_Cost THEN '1' 
						ELSE '0' 
						END) AS ismatched"))->where("dw_Product.Product_AC_4", $productParentCode)->orderBy("LG_Date", "DESC")->get();
        //Loop To Add Sum of column for inventory table
        $inCnt = !empty($inventory) && isset($inventory) ? sizeof($inventory) : 0;
        $avg = $physical_Stock = $allocation_Stock = $free_stock = $allocation_After = $on_Order = $true_Cost = $std_Cost = $avg_Cost = 0;
        if (!empty($inventory) && isset($inventory)) {
            for ($f = 0; $f < $inCnt; $f++) {
                $physical_Stock += $inventory[$f]->Physical_Stock;
                $allocation_Stock += $inventory[$f]->Allocation_Stock;
                $free_stock += $inventory[$f]->Free_stock;
                $allocation_After += $inventory[$f]->Allocation_After;
                $on_Order += $inventory[$f]->On_Order;
                $true_Cost += $inventory[$f]->True_Cost / $inCnt;
                $std_Cost += $inventory[$f]->Std_Cost / $inCnt;
                $avg_Cost += $inventory[$f]->Avg_Cost / $inCnt;
            }
        }

        return ["inventory_data" => $inventory, "avg_phy_stock" => $physical_Stock, "avg_allo_stock" => $allocation_Stock, "avg_free_stock" => $free_stock, "avg_allo_after" => $allocation_After, "avg_on_order" => $on_Order, "avg_true_cost" => number_format($true_Cost, 2), "avg_std_cost" => number_format($std_Cost, 2), "avg_avg_cost" => number_format($avg_Cost, 2)];
    }

    /**
     * Gets the inventory details
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getInventoryData($productid)
    {

        $physical_Stock1 = $physical_StockOther = $allocation_Stock = $free_stock = $allocation_After = $on_Order = $true_Cost = $std_Cost = $avg_Cost = $phyStock1 = $phyStock = $phyStockOther = $ph_StockOther_in = 0;
        $supplier = '';
        $inventory = $data = $inventoryData = [];
        $productParentCode = Product::where(["prod_id" => $productid])->pluck("products.ac4")->first();
        $depot = DwDepot::where(["Depot_Code" => 1])->pluck("Depot_Id")->first();

        //$depot = 24;
        //Get last day of previous month
        $lstPrvMon = date('Y-m-d', strtotime('last day of previous month'));

        //Dataware house Inventory data

        /* $inventory = DwInventory::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=','staging.dw_Inventory.Product_Id')->select("dw_Product.Product_Code", "Physical_Stock", "Allocation_Stock", "Allocation_After", DB::raw("Physical_Stock - Allocation_Stock as Free_stock"), "On_Order", "True_Cost", "Std_Cost", DB::raw("REPLACE(LG_Date, '1900-01-01', '-') as LG_Date"))->where("dw_Product.Product_AC_4", $productParentCode)->where("LG_Date", ">", $lstPrvMon)->orderBy("LG_Date", "DESC")->get(); 
         */
        $inventory = DwInventory::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=', 'staging.dw_Inventory.Product_Id')
                        ->leftJoin('staging.dw_Depot', 'dw_Depot.Depot_Id', '=', 'staging.dw_Inventory.Depot_Id')->select("dw_Product.Product_Code", "dw_Product.Product_AC_5 as supplier", "dw_Inventory.Depot_Id", 
                                "dw_Depot.Depot_Code", "Physical_Stock", "Allocation_Stock", "Allocation_After",
                                DB::raw("Physical_Stock - Allocation_Stock as Free_stock"),
                                "On_Order", DB::raw("REPLACE(REPLACE(CAST(True_Cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS True_Cost"), 
                                DB::raw("REPLACE(REPLACE(CAST(Std_Cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Std_Cost"), 
                                 DB::raw("REPLACE(REPLACE(CAST(Avg_Cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Avg_Cost"),
                             
                                DB::raw("REPLACE(LG_Date, '1900-01-01', '-') as LG_Date"))->where("dw_Product.Product_AC_4", $productParentCode)->orderBy("LG_Date", "DESC")->get()->toArray();

        //Gets the Ghost priorities
        $ghosts = Ghost::where(["ghost_agg_code" => $productParentCode, "is_latest" => 1])->select("ssf_alt_prod_code_1", "ssf_alt_prod_code_2", "ssf_alt_prod_code_3")->first();
        $ghosts = !empty($ghosts) ? $ghosts->toArray() : [];
        $ghPr1 = !empty($ghosts['ssf_alt_prod_code_1']) && isset($ghosts['ssf_alt_prod_code_1']) ? $ghosts['ssf_alt_prod_code_1'] : '';
        $ghPr2 = !empty($ghosts['ssf_alt_prod_code_2']) && isset($ghosts['ssf_alt_prod_code_2']) ? $ghosts['ssf_alt_prod_code_2'] : '';
        $ghPr3 = !empty($ghosts['ssf_alt_prod_code_3']) && isset($ghosts['ssf_alt_prod_code_3']) ? $ghosts['ssf_alt_prod_code_3'] : '';

        $ghPriority = '';

        //Gets the count of total inventory items for AC4
        $inCnt = !empty($inventory) && isset($inventory) ? sizeof($inventory) : 0;
        $priority = [];
        foreach ($inventory as $inventoryItem) {

            $productCode = !empty($inventoryItem['Product_Code']) && isset($inventoryItem['Product_Code']) ? $inventoryItem['Product_Code'] : 'pcode';

            $physicalStock = !empty($inventoryItem['Physical_Stock']) && isset($inventoryItem['Physical_Stock']) ? $inventoryItem['Physical_Stock'] : 0;
            $allocationStock = !empty($inventoryItem['Allocation_Stock']) && isset($inventoryItem['Allocation_Stock']) ? $inventoryItem['Allocation_Stock'] : 0;
            $allocationAfter = !empty($inventoryItem['Allocation_After']) && isset($inventoryItem['Allocation_After']) ? $inventoryItem['Allocation_After'] : 0;
            $freeStock = !empty($inventoryItem['Free_stock']) && isset($inventoryItem['Free_stock']) ? $inventoryItem['Free_stock'] : 0;
            $onOrder = !empty($inventoryItem['On_Order']) && isset($inventoryItem['On_Order']) ? $inventoryItem['On_Order'] : 0;
            $trueCost = !empty($inventoryItem['True_Cost']) && isset($inventoryItem['True_Cost']) ? $inventoryItem['True_Cost'] : 0;
            $stdCost = !empty($inventoryItem['Std_Cost']) && isset($inventoryItem['Std_Cost']) ? $inventoryItem['Std_Cost'] : 0;
            $avgCost = !empty($inventoryItem['Avg_Cost']) && isset($inventoryItem['Avg_Cost']) ? $inventoryItem['Avg_Cost'] : 0;

            if ($ghPr1 == $productCode) {
                $priority[$productCode] = 1;
            } else if ($ghPr2 == $productCode) {
                $priority[$productCode] = 2;
            } else if ($ghPr3 == $productCode) {
                $priority[$productCode] = 3;
            } else {
                $priority[$productCode] = 0;
            }



            $inventoryData[$productCode][] = $inventoryItem;

            //Finding the total for stock data and average for cost data

            $allocation_Stock += $allocationStock;
            $free_stock += $freeStock;
            $allocation_After += $allocationAfter;
            $on_Order += $onOrder;
            $true_Cost += $trueCost / $inCnt;
            $std_Cost += $stdCost / $inCnt;
            $avg_Cost += $avgCost / $inCnt;
        }

        $avg_Cost = number_format($avg_Cost, 2);
        $std_Cost = number_format($std_Cost, 2);
        $true_Cost = number_format($true_Cost, 2);
        $ismatched = 0;

        foreach ($inventoryData as $pcode => $item) {
            $phyStockOther = 0;
            foreach ($item as $invt) {

                $depot = !empty($invt['Depot_Id']) && isset($invt['Depot_Id']) ? $invt['Depot_Id'] : '';
                $supplier = !empty($invt['supplier']) && isset($invt['supplier']) ? $invt['supplier'] : '';
                $depotCode = !empty($invt['Depot_Code']) && isset($invt['Depot_Code']) ? $invt['Depot_Code'] : '';

                //Depo1
                if ($depotCode == 01) {
                    $phyStock1 = !empty($invt['Physical_Stock']) && isset($invt['Physical_Stock']) ? (int) $invt['Physical_Stock'] : 0;

                    $physical_Stock1 += $phyStock1;
                    $allocationStock = !empty($invt['Allocation_Stock']) && isset($invt['Allocation_Stock']) ? $invt['Allocation_Stock'] : 0;
                    $allocationAfter = !empty($invt['Allocation_After']) && isset($invt['Allocation_After']) ? $invt['Allocation_After'] : 0;
                    $freeStock = !empty($invt['Free_stock']) && isset($invt['Free_stock']) ? $invt['Free_stock'] : 0;
                    $onOrder = !empty($invt['On_Order']) && isset($invt['On_Order']) ? $invt['On_Order'] : 0;
                    $trueCost = !empty($invt['True_Cost']) && isset($invt['True_Cost']) ? $invt['True_Cost'] : 0;
                    $stdCost = !empty($invt['Std_Cost']) && isset($invt['Std_Cost']) ? $invt['Std_Cost'] : 0;
                    $avgCost = !empty($invt['Avg_Cost']) && isset($invt['Avg_Cost']) ? $invt['Avg_Cost'] : 0;
                    $lgDate = !empty($invt['LG_Date']) && isset($invt['LG_Date']) ? $invt['LG_Date'] : '';
                    if ($trueCost == $stdCost) {
                        $ismatched = 1;
                    }
                    //Other depos
                } else {
                    $phyStockOther = !empty($invt['Physical_Stock']) && isset($invt['Physical_Stock']) ? (int) $invt['Physical_Stock'] : 0;
                    //$phyStockOther += $phyStockO;
                    $physical_StockOther += $phyStockOther;
                    $ph_StockOther_in += $phyStockOther;
                }
            }
            $data[] = ['Product_Code' => $pcode,
                'supplier' => $supplier,
                'phyStock1' => $phyStock1,
                'phyStockOther' => $ph_StockOther_in,
                "Allocation_Stock" => $allocationStock,
                "Allocation_After" => $allocationAfter,
                "Free_stock" => $freeStock,
                "On_Order" => $onOrder,
                "True_Cost" => $trueCost,
                "Std_Cost" => $stdCost,
                "Avg_Cost" => $avgCost,
                "LG_Date" => $lgDate,
                "ismatched" => $ismatched,
                "gh_priority" => !empty($priority[$pcode]) && isset($priority[$pcode]) ? $priority[$pcode] : 0
            ];
            $ph_StockOther_in = 0;
        }
        $ari = DB::table('ari as a')
                        ->join('DwProduct as p', 'p.Product_Id', '=', 'a.product_id')
                        ->leftjoin("products as pp", function ($join) {
                            $join->on("pp.ac4", "=", "p.Product_AC_4")
                            ->on("pp.is_parent", "=", DB::raw(1));
                        })->where("pp.prod_id", $productid)->pluck(DB::raw("REPLACE(a.ARIVOLUME, 'SEE COLOUMN I', '-') AS volume"))->first();

        return ["inventory_data" => $data, "total_phy_stock1" => $physical_Stock1, "total_phy_stock_other" => $physical_StockOther, "total_allo_stock" => $allocation_Stock, "total_free_stock" => $free_stock, "total_allo_after" => $allocation_After, "total_on_order" => $on_Order, "avg_true_cost" => $true_Cost, "avg_std_cost" => $std_Cost, "avg_avg_cost" => $avg_Cost, "ari_volume" => $ari];
    }
    
    
     /**
     * Gets the Product ARI Volume
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getProductARIVolume($productid)
    {
   // $ari = '';
    
    $ari = DB::table('ari as a')
                        ->join('DwProduct as p', 'p.Product_Id', '=', 'a.product_id')
                        ->leftjoin("products as pp", function ($join) {
                            $join->on("pp.ac4", "=", "p.Product_AC_4")
                            ->on("pp.is_parent", "=", DB::raw(1));
                        })->where("pp.prod_id", $productid)->pluck(DB::raw("REPLACE(a.ARIVOLUME, 'SEE COLOUMN I', '-') AS volume"))
                                ->first();
                   

        return ["ari_volume" => $ari];
    }

    /**
     * Gets the GRN details
     *
     * @param \App\Models\Product $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getGRNData($productid)
    {
        $grn = [];
        $Grn_Price = $Grn_Qty = 0;
        $productObj = Product::select("products.ac4 as parent_product_code")->where(["prod_id" => $productid])->first();
        //Get last day of previous month
        //$lstPrvMon = date('Y-m-d', strtotime('last day of previous month'));
        $prevThreeMonDate = date('Y-m-d', strtotime('-3 month'));

        $productParentCode = !empty($productObj) ? $productObj->parent_product_code : "";

        //Dataware house GRN
        /* $grn = DwGRN::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=', 'GRN.Product_Id')->join('staging.dw_Supplier', 'dw_Supplier.Supplier_Id', '=', 'GRN.Supplier_Id')->select("dw_Product.Product_Code", "dw_Supplier.Supplier_Code", "Grn_No", "Grn_Price", "Grn_Qty", "Receipt_Date")->where("dw_Product.Product_AC_4" , $productParentCode)->where("GRN.Receipt_Date", ">", $lstPrvMon)->orderBy("Receipt_Date", "DESC")->get(); */
        /* $grn =DwGRN::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=', 'staging.dw_GRN.Product_Id')->leftJoin('staging.dw_Supplier', 'dw_Supplier.Supplier_Id', '=', 'staging.dw_GRN.Supplier_Id')->select("dw_Product.Product_Code","dw_Supplier.Supplier_Id","dw_Supplier.Supplier_Code", "Grn_No", "Grn_Price", "Grn_Qty", "Receipt_Date")->where("dw_Product.Product_AC_4", $productParentCode)->where("staging.dw_GRN.Receipt_Date", ">", $lstPrvMon)->orderBy("Receipt_Date", "DESC")->get(); */

        /* $grn = GRNMain::leftJoin('staging.dw_Product', 'dw_Product.Product_Id', '=', 'GRNMain.Product_Id')->leftJoin('staging.dw_Supplier', 'dw_Supplier.Supplier_Id', '=', 'GRNMain.Supplier_Id')->select("dw_Product.Product_Code","dw_Supplier.Supplier_Id","dw_Supplier.Supplier_Code", "Grn_No", "Grn_Price", "Grn_Qty", "Receipt_Date")->where("dw_Product.Product_AC_4", $productParentCode)->orderBy("Receipt_Date", "DESC")->limit(20)->get(); */
        //->toSql(); to print query

        $grn = GRN::select("Product_Code", "Supplier_Id", "Supplier_Id_Main", "Supplier_Code", "Grn_No",
//                "Grn_Price",
                  DB::raw("REPLACE(REPLACE(CAST(Grn_Price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Grn_Price"),
                "Grn_Qty", "Receipt_Date", "Product_Desc", "Pack_Size")->where("Product_AC_4", $productParentCode)->where("GRN.Receipt_Date", ">", $prevThreeMonDate)->orderBy("Receipt_Date", "DESC")->get();

        if (empty($grn) || count($grn) < 1) {
            $grn = GRN::select("Product_Code", "Supplier_Id", "Supplier_Id_Main", "Supplier_Code", "Grn_No", "Grn_Price", "Grn_Qty", "Receipt_Date", "Product_Desc", "Pack_Size")->where("Product_AC_4", $productParentCode)->orderBy("Receipt_Date", "DESC")->limit(25)->get();
        }
        return $grn;
    }

    /**
     * Get the all active Tag List, Severity List and Active as well as Historical Tag added for product
     *
     * @param  \App\Models\Product $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getTagDetails($productid)
    {
        //$tags = [];
        $taglist = $severity = $product_current_tag = $product_historical_tag = [];
        $taglist = ProductTags::getAllTag();
        $severity = ProductTags::getAllSeverity();
        $product_current_tag = ProductTags::getAllCurrentTag($productid);
        $product_historical_tag_latest_one_month = ProductTags::getHistoricalTag($productid);
        $product_historical_tag_before_one_month = ProductTags::getAllHistoricalTag($productid);

        return ["tags" => $taglist, "severities" => $severity, "active_tags" => $product_current_tag, "historical_tags_of_last_month" => $product_historical_tag_latest_one_month, "historical_all_tags" => $product_historical_tag_before_one_month];
    }

    /**
     * Gets the header details like product code, and source name
     *
     * @param int $productId The Product ID
     * @param int $sourceId The Source ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPriceCaptureHeader($productId, $sourceId)
    {
        $header = [];
        $ac4Code = Product::where(["prod_id" => $productId])->pluck("products.ac4")->first();
        $source = PricingSource::where(["id" => $sourceId])->pluck("name")->first();
        $header = ["ac4" => $ac4Code, "source" => $source];
        return $header;
    }

    /**
     * Gets the Historical price capture details of product and source
     *
     * @param int $productId The Product ID
     * @param int $sourceId The Source ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPriceCaptureHistoricalData($productId, $sourceId)
    {
        $pricingdata = [];
        $ac4Code = Product::where(["prod_id" => $productId])->pluck("products.ac4")->first();
        $source = PricingSource::where(["id" => $sourceId])->pluck("name")->first();

        //Price Capture Pricing data
        $pricingdata = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')->leftjoin('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')->select("suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "comments")->where(["pricing_data.parent_product_code" => $ac4Code, "pricing_data.source_id" => $sourceId])->orderBy("price_from_date", "DESC")
                ->get();
        return $pricingdata;
    }

    public static function validateDataForNegotiatedPrice($ac4, $product_code, $source_id, $supplier_id, $negotiated_price)
    {

        $errors = [];

        if (empty($ac4) && empty($product_code)) {
            $errors[] = 'Either Aggregate Code or Product Code should be provided';
        }

        if (!empty($ac4)) {
            $ac4Obj = Product::where(["ac4" => $ac4])->select('prod_id')->first();

            if (empty($ac4Obj)) {
                $errors[] = 'Provided Aggregate Code doesn\'t exist in the system';
            }
        }


        if (!empty($product_code)) {
            $product = Product::where(["product_code" => $product_code])->select('prod_id')->first();

            if (empty($product)) {
                $errors[] = 'Provided Product Code doesn\'t exist in the system';
            }
        }


        if (empty($source_id) || empty($supplier_id) || empty($negotiated_price)) {
            $errors[] = 'Source, Supplier & negotiated price should be provided';
        }
        if (!empty($source_id)) {
            $source = PricingSource::where(["id" => $source_id])->select('id')->first();

            if (empty($source)) {
                $errors[] = 'Provided source doesn\'t exist in the system';
            }
        }

        if (!empty($supplier_id)) {
            $supplier = DB::table('suppliers')->where(["id" => $supplier_id])->select('id')->first();

            if (empty($supplier)) {
                $errors[] = 'Provided supplier doesn\'t exist in the system';
            }
        }
        if (!empty($negotiated_price) && (!is_numeric($negotiated_price) || $negotiated_price < 0)) {
            $errors[] = 'Provided negotiated_price is invalid';
        }
        if ((!empty($ac4) || !empty($product_code)) && !empty($source_id) && !empty($supplier_id) && !empty($negotiated_price)) {
            $pricingItem = DB::table('pricing_data')
                            ->where(["parent_product_code" => $ac4, "product_code" => $product_code, "source_id" => $source_id, "supplier_id" => $supplier_id])->select("id")->first();
            if (empty($pricingItem)) {
                $errors[] = 'Pricing record doesn\'t exist in the system';
            }
        }
        return $errors;
    }

    /**
     * Gets the cheapest supplier and price
     *
     * @return array The array of cheapest supplier and price
     */
    public static function getCheapestPriceNSupplier($spData = NULL)
    {
        $spDataArray = !empty($spData) && !is_array($spData) ? $spData->toArray() : $spData;
        $prices = array_column($spDataArray, "price", "supp_code");
        asort($prices);

        $cheapestSupplier = array_key_first($prices);
        $cheapestPrice = !empty($prices) && isset($prices[$cheapestSupplier]) ? $prices[$cheapestSupplier] : "";
        return ["cheapestSupplier" => $cheapestSupplier, "cheapestPrice" => $cheapestPrice];
    }

    /**
     * Gets the details of the contract given on a particular product
     *
     * @return array The array of contracts of the product code for different customers
     */
    public static function getContractDetailsOld($pr_id)
    {
        $result = [];
        $prod = Product::where(["prod_id" => $pr_id])->select("ac4", "product_code")->first();
        $ac4 = !empty($prod->ac4) && isset($prod->ac4) ? $prod->ac4 : '';
        $productCode = !empty($prod->product_code) && isset($prod->product_code) ? $prod->product_code : '';

        if (!empty($ac4) && !empty($productCode)) {
            $tables = DB::table('contract_table_info')
                            ->pluck("table_name")->toArray();
//
            (string) $select = "select cpsc.sigma_product_code, CAST(cpsc.trade_price AS DECIMAL(10,2)) AS trade_price";
            (string) $join = " from productsc_distinct as cpsc";
//
            $tables_count = sizeof($tables);
            $i = 0;
            while ($tables_count) {
                $table_name = $tables[$i];
                $table_alias = "c_" . substr($table_name, strpos($table_name, '_') + 1);

                $select = $select . ", CAST($table_name.contract_price AS DECIMAL(10,2)) AS $table_alias";
                $tables_count--;
                $i++;
            }
//
            $i = 0;
            $tables_count = sizeof($tables);
            while ($tables_count) {
                $table_name = $tables[$i];

                $join = $join . " left join $table_name on cpsc.sigma_product_code = $table_name.product_code";
                $tables_count--;
                $i++;
            }
//
            $where = " where cpsc.product_analysis_group_5 LIKE '%SPOT%' and cpsc.product_analysis_group_4 = '$ac4' ";

            $query = $select . $join . $where;

            $result = DB::select("$query");

//                            ->pluck("table_name")->toArray();
        }
        return $result;
    }

    /**
     * Gets the details of the contract given on a particular product
     *
     * @return array The array of contracts of the product code for different customers
     */
    public static function getContractDetails($pr_id)
    {
        $result = [];
        $prod = Product::where(["prod_id" => $pr_id])->select("ac4", "product_code")->first();
        $ac4 = !empty($prod->ac4) && isset($prod->ac4) ? $prod->ac4 : '';

        if (!empty($ac4)) {


            $result = DB::table('group_pricing as g')->where(['agg_code' => $ac4, 'spot' => 'SPOT'])
                    ->orderBy("g.asofdate", "DESC")
                    ->select('g.product_code as sigma_product_code',
                            DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','-')  AS ATOZ"),
                            DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','-')  AS RRP"),
                            DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','-')  AS c87"),
                            DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','-')  AS c122"),
                            DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','-')  AS DC"),
                            DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','-')  AS DG"),
                            DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00',' - ')  AS RH"),
                            DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00',' - ')  AS RBS")
                    )
                    ->first();
        }
        return $result;
    }

    /**
     * Gets the details of the kpi given on a particular product
     *
     * @return array The array of contracts of the product code for different customers
     */
    public static function getKpiDetails($pr_id)
    {
        $ac4 = Product::where(["prod_id" => $pr_id])->pluck("products.ac4")->first();
        $tables = [];
        if (!empty($ac4)) {
            $tables = DB::table('kpi')
                            ->where(["sm_analysis_code2" => $ac4])
                            ->select("kpi_id", DB::raw("REPLACE(target_percentage, '%', '') as target_percentage"), "target_volume")->first();
        }
        return $tables;
    }

    /**
     * Gets the  price indicator value in product header  like Up | Down | Straight
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPriceIndicator($productid)
    {
        $priceIndicator = '';
        $priceIndicator = DB::table('product_price_indicator')
                        ->where(["product_id" => $productid])->whereNull("end_date")
                        ->pluck("price_indicator")->first();
        return $priceIndicator;
    }

    /**
     * Gets the ARI indicator for the product
     * ARI indicator which identifies the supplier that is preferable for that product out of all the suppliers for that product
     *
     * @return array  The array of ARI indicator data
     */
    public static function getARIIndicator($prodid)
    {

        $ari = [];
        $ari["productid"] = $prodid;
        $ari["current"] = DB::table('ari_indicator')->leftjoin('suppliers', 'suppliers.id', '=', 'ari_indicator.supplier_id')
                        ->where("product_id", $prodid)->where("is_ari_supplier", 1)
                        ->select("ari_id", "suppliers.code as supp_code", "suppliers.name as supp_name")->orderBy("supp_code")->get();

        $ari["past"] = DB::table('ari_indicator')->leftjoin('suppliers', 'suppliers.id', '=', 'ari_indicator.supplier_id')
                        ->where("product_id", $prodid)->where("is_ari_supplier", 0)
                        ->select("ari_id", "suppliers.code as supp_code", "suppliers.name as supp_name")->orderBy("ari_indicator.updated_at", "DESC")->limit(4)->get();
        return $ari;
    }

    /**
     * Gets the ARI indicator for the product
     * ARI indicator which identifies the supplier that is preferable for that product out of all the suppliers for that product
     *
     * @return array  The array of ARI indicator data
     */
    public static function getARIInfo($prodid)
    {

        $ari = [];
        $ari["productid"] = $prodid;
        $ari["latest"] = DB::table('ari as a')
                        ->join('DwProduct as p', 'p.Product_Id', '=', 'a.product_id')
                        ->leftjoin("products as pp", function ($join) {
                            $join->on("pp.ac4", "=", "p.Product_AC_4")
                            ->on("pp.is_parent", "=", DB::raw(1));
                        })->where("pp.prod_id", $prodid)
                        ->select('a.CODE as livery', 'a.BRAND as supplier',
                                  DB::raw("REPLACE(REPLACE(CAST(a.ARIPRICE AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
//                                'a.ARIPRICE as price',
                                DB::raw("REPLACE(a.ARIVOLUME, 'SEE COLOUMN I', '-') AS volume"),
                                'a.Comments as comments'
                        )
                        ->orderBy("a.created_at", "DESC")->limit(1)->get();

        return $ari;
    }

    /**
     * Gets the ARI indicator for the product
     * ARI indicator which identifies the supplier that is preferable for that product out of all the suppliers for that product
     *
     * @return array  The array of ARI indicator data
     */
    public static function getHistARIInfo($prodid)
    {

        $ari = [];
        $ari["productid"] = $prodid;
        $ari["all"] = DB::table('ari as a')
                        ->join('DwProduct as p', 'p.Product_Id', '=', 'a.product_id')
                        ->leftjoin("products as pp", function ($join) {
                            $join->on("pp.ac4", "=", "p.Product_AC_4")
                            ->on("pp.is_parent", "=", DB::raw(1));
                        })->where("pp.prod_id", $prodid)
                        ->select('a.AGG CODE as AggCode', 'a.CODE as livery', 'a.BRAND as supplier', 'a.ARIPRICE as price',
                                DB::raw("REPLACE(a.ARIVOLUME, 'SEE COLOUMN I', '-') AS volume"),
                                'a.Comments as comments'
                        )
                        ->orderBy("a.created_at", "DESC")->get();

        return $ari;
    }

    public static function searchCompetitorPricingByDate($group, $sdate, $edate, $page, $sortcolumn, $sorder, $limit)
    {
        $prdata = [];
        if ($group == 7) {

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.COST AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[TRUE COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG VOL] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[87] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[122] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'g.BrokenRule',
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                           ->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.status', '=', 0)
                    ->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                    ->count();
        }  else {
        $products =  DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                            ->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = self::getBuyerComment($products->product_id);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = self::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;
            $products->sensitivity = 'No';
            $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock];
            $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock];
            $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock];
            $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock];
            $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock];
            if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                $products->sensitivity = 'Yes';
            }

            return $products;
        });
        $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                ->where('wl.list_type', '=', $group)
                ->where('wl.is_deleted', 0)
                ->where('wl.status', '=', 0)
                ->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                ->count();
        }
        
        $prdata['products'] = $products;
        $prdata['count'] = $pcount;
        return $prdata;
    }

    public static function searchCompetitorPricingByProduct($group, $prodcode, $page, $sortcolumn, $sorder, $limit)
    {
        $prdata = [];
        
        if ($group == 7) {

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.COST AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[TRUE COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG VOL] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[87] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[122] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'g.BrokenRule',
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                            ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.status', '=', 0)
                     ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })
                    ->count();
        } else {

        $products =  DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                           ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
            $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock];
            $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock];
            $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock];
            $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock];
            $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock];
            if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                $products->sensitivity = 'Yes';
            }

            return $products;
        });
        $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                ->where('wl.list_type', '=', $group)
                ->where('wl.is_deleted', 0)
                ->where('wl.status', '=', 0)
                ->where(function ($query) use ($prodcode) {
                    $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                })
                ->count();

        }
        $prdata['products'] = $products;
        $prdata['count'] = $pcount;
        return $prdata;
    }

    public static function searchCompetitorPricingByProductDate($group, $prodcode, $sdate, $edate, $page, $sortcolumn, $sorder, $limit)
    {
        $prdata = [];
        
        if ($group == 7) {

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.COST AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[TRUE COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG VOL] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[87] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[122] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'g.BrokenRule',
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                           ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.status', '=', 0)
                    ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                    ->count();
        } else {

      $products =  DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                            ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')
                                    ->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')
                                    ->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = self::getBuyerComment($products->product_id);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = self::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;
            $products->sensitivity = 'No';
            $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock];
            $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock];
            $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock];
            $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock];
            $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock];
            if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                $products->sensitivity = 'Yes';
            }

            return $products;
        });
        $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                ->where('wl.list_type', '=', $group)
                ->where('wl.is_deleted', 0)
                ->where('wl.status', '=', 0)
                ->where(function ($query) use ($prodcode) {
                    $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')
                        ->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')
                        ->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                })
                ->where("wl.as_of_date", '>=', $sdate)->where("wl.as_of_date", '<=', $edate)
                ->count();
       
        }
        $prdata['products'] = $products;
        $prdata['count'] = $pcount;
        return $prdata;
    }

    public static function downloadCompetitorPricing($group, $sdate, $edate)
    {
        $productsExport = [];

        if ($group == 2) {
            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) {
                                $join->on('p.Product_Id', '=', 'cp.product_id')
                                ->whereRaw(
                                        'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
                                );
                            })
//                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
//                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
//                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "TrueCost"'),
//                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "AvgCost"'),
//                                    'i.Average_usage as AvgVol',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                    DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                    DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                    DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                    DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                    DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.AsOfDate as AsOfDate'
                            )->where('cp.group', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->orderBy('cp.AsOfDate', 'ASC')
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';

                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });
        } else if ($group == 1) {
            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                           ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                         
                                ->on("cp.group", DB::raw("'$group'"))
                                        ->whereRaw(
                                       'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
                                );
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.AsOfDate as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.as_of_date', '>=', $sdate)->where('wl.as_of_date', '<=', $edate)
                            ->orderBy('wl.as_of_date', 'ASC')
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';

                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });
        } else if($group == 7) {
             $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                         
                                ->on("cp.group", DB::raw("'$group'"))
                                        ->whereRaw(
                                       'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
                                );
                            })
//                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
//                                $join->on('wl.product_id', '=', 'cp.product_id')
//                                ->on('cp.AsOfDate', '=', 'wl.as_of_date')
//                                ->on("cp.group", DB::raw("'$group'"));
//                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.COST AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[TRUE COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG VOL] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),

                                    DB::raw("REPLACE(REPLACE(CAST(g.[87] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[122] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'g.BrokenRule',
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.as_of_date', '>=', $sdate)->where('wl.as_of_date', '<=', $edate)
                            ->orderBy('wl.as_of_date', 'ASC')
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
//                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
//                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
//                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
//                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
//                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });
        } else {
            $products = DB::table('dbo.competitor_prices as cp')
                    ->leftjoin("DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                    ->leftjoin('group_pricing as g', function ($join) {
                        $join->on('p.Product_Code', '=', 'g.product_code')
                        ->whereRaw(
                                'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                        );
                    })
                    ->select('g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                            DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
                            DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
                            DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
                            DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
                            DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                            'cp.phoenix_outofstock',
                            'cp.trident_outofstock',
                            'cp.aah_outofstock',
                            'cp.colorama_outofstock',
                            'cp.bestway_outofstock',
                            'cp.AsOfDate'
                    )->where('cp.group', '=', $group)
                    ->where('cp.AsOfDate', '>=', $sdate)->where('cp.AsOfDate', '<=', $edate)
                    ->orderBy('cp.AsOfDate', 'ASC')
                    ->get();
        }


      
        foreach ($products as $key => $item) {
            $item = json_decode(json_encode($item), true);

            if (($item['phoenix_outofstock'] + $item['trident_outofstock'] + $item['aah_outofstock'] + $item['colorama_outofstock'] + $item['bestway_outofstock'] ) > 1) {
                $item['sensitivity'] = "Yes";
            } else {
                $item['sensitivity'] = "No";
            }
            if (!empty($item['AsOfDate']) && isset($item['AsOfDate'])) {
                $productsExport[$item['AsOfDate']][] = $item;
            }
        }



        return $productsExport;
    }

    /**
     * Gets the product page header details like product code, description and dt_pack
     *
     * @param \App\Models\Product  $productid The Product ID
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAnalyticsPageHeader($prodcode)
    {
        $productObj = Product::leftjoin("vw_sigma_group_pricing as g", 'g.product_code', '=', 'products.product_code')
                        ->select('g.ranking as Ranking', "products.prod_id", "products.ac4 as parent_product_code", "products.product_code as spot_code", "clean_description", "products.dt_pack", "products.pack_size")->where(["products.product_code" => $prodcode])->first();

        $header = !empty($productObj) ? $productObj->toArray() : [];
        //If DT PACK is zero, sets it to "NA"

        $header["dt_pack"] = !empty($header["dt_pack"]) ? $header["dt_pack"] : '';
        if (empty($header["dt_pack"])) {
            $header["dt_pack"] = !empty($header["pack_size"]) ? $header["pack_size"] : "NA";
        }
        return $header;
    }

    public static function searchWatchlistByDate($type, $sdate, $edate, $page, $sortcolumn, $sorder, $limit)
    {
        $prdata = [];
        $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate'
                        )->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                        ->where('pw.as_of_date', '>=', $sdate)->where('pw.as_of_date', '<=', $edate)
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->pid);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = Product::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;

            return $products;
        });

        $rowCnt = DB::table('product_watchlist as pw')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                ->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->where('pw.as_of_date', '>=', $sdate)->where('pw.as_of_date', '<=', $edate)
                ->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->count();
        $prdata['products'] = $products;
        $prdata['count'] = $rowCnt;
        return $prdata;
    }

    public static function searchWatchlistByProduct($type, $prodcode, $page, $sortcolumn, $sorder, $limit)
    {
        $prdata = [];

        $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate'
                        )->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                        ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->pid);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = Product::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;

            return $products;
        });

        $rowCnt = DB::table('product_watchlist as pw')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                ->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->where(function ($query) use ($prodcode) {
                    $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                })->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->count();

        $prdata['products'] = $products;
        $prdata['count'] = $rowCnt;
        return $prdata;
    }

    public static function searchWatchlistByProductDate($type, $prodcode, $sdate, $edate, $page, $sortcolumn, $sorder, $limit)
    {
        $prdata = [];

        $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate'
                        )->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                        ->where('pw.as_of_date', '>=', $sdate)->where('pw.as_of_date', '<=', $edate)
                        ->where(function ($query) use ($prodcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                        })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->pid);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = Product::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;

            return $products;
        });

        $rowCnt = DB::table('product_watchlist as pw')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                ->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->where('pw.as_of_date', '>=', $sdate)->where('pw.as_of_date', '<=', $edate)
                ->where(function ($query) use ($prodcode) {
                    $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                })->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->count();
        $prdata['products'] = $products;
        $prdata['count'] = $rowCnt;
        return $prdata;
    }

    public static function getCompetitorPricingData($group, $page, $sortcolumn, $sorder)
    {

        $cpdata = $products = $pcount = [];
        $today = date("Y-m-d");
        $limit = 10;
        $sortcolumn = trim($sortcolumn);
         if($sortcolumn == 'ProductAC4') {
               $sortcolumn = 'Product_AC_4';
         }
         if($sortcolumn == 'AsOfDate') {
               $sortcolumn = 'wl.as_of_date';
         }
        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sorder == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        if ($group == 1 || $group == 4 ) {

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
//                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
//                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
//                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "TrueCost"'),
//                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "AvgCost"'),
//                                    'i.Average_usage as AvgVol',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                     'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate',
                                      'wl.created_at'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
//                             ->where('wl.actioned', '=', 0)       
                            //->where('wl.as_of_date', $today)
                            ->orderBy($sortcolumn, $sorder)
                            ->orderBy(DB::raw('CAST(wl.created_at AS TIME)'), 'asc')
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                    ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                    ->leftjoin('group_pricing as g', function ($join) {
                        $join->on('p.Product_Code', '=', 'g.product_code')
                        ->whereRaw(
                                'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                        );
                    })
                    ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                        $join->on('wl.product_id', '=', 'cp.product_id')
                        ->on("cp.actioned", DB::raw("'0'"))
                        ->on("cp.group", DB::raw("'$group'"));
                    })
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.status', '=', 0)
//                    ->where('wl.actioned', '=', 0)   
                    //->where('wl.as_of_date', $today)
                    ->count();
        }
        
        
        if ($group == 7) {

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.COST AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[TRUE COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG VOL] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[87] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[122] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'g.BrokenRule',
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
//                             ->where('wl.actioned', '=', 0)       
                            //->where('wl.as_of_date', $today)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
        
                            ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'wl.as_of_date');
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.status', '=', 0)
//                    ->where('wl.actioned', '=', 0)   
                    //->where('wl.as_of_date', $today)
                    ->count();
        }


        //Buyer defined
        if ( $group == 2) {

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "TrueCost"'),
                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "AvgCost"'),
                                    'i.Average_usage as AvgVol',
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                    DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                    DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                    DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                    DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                                    DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                    DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                                    DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                                     DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                     DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
//                                    DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
//                                    DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
//                                    DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
//                                    DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
//                                    DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.status', '=', 0)
                            //->where('wl.as_of_date', $today)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                   $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                    ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                    ->leftjoin('group_pricing as g', function ($join) {
                        $join->on('p.Product_Code', '=', 'g.product_code')
                        ->whereRaw(
                                'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                        );
                    })
                    ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                        $join->on('wl.product_id', '=', 'cp.product_id')
                        ->on("cp.actioned", DB::raw("'0'"))
                        ->on("cp.group", DB::raw("'$group'"));
                    })
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.status', '=', 0)
                    //->where('wl.as_of_date', $today)
                    ->count();
        }

        if ($group == 6) {
            $day = strtolower(date('l'));

            $products = DB::table('dbo.product_watchlist as wl')
                            ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                $join->on('wl.product_id', '=', 'cp.product_id')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "TrueCost"'),
                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "AvgCost"'),
                                    'i.Average_usage as AvgVol',
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                    DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                    DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                    DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                    DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                                    DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                    DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                                    DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
//                                    DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS phoenix'),
//                                    DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS trident'),
//                                    DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS aah'),
//                                    DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS colorama'),
//                                    DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS bestway'),
                                     DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                     DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'cp.phoenix_note',
                                    'cp.trident_note',
                                    'cp.aah_note',
                                    'cp.colorama_note',
                                    'cp.bestway_note',
                                    'wl.as_of_date as AsOfDate'
                            )->where('wl.list_type', '=', $group)
                            ->where('wl.is_deleted', 0)
                            ->where('wl.day', $day)
                            ->where('wl.status', '=', 0)
                            //->where('wl.as_of_date', $today)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                  $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock,  'note' => $products->phoenix_note];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock, 'note' => $products->trident_note];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock, 'note' => $products->aah_note];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock, 'note' => $products->colorama_note];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock, 'note' => $products->bestway_note];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.product_watchlist as wl')
                    ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                    ->leftjoin('group_pricing as g', function ($join) {
                        $join->on('p.Product_Code', '=', 'g.product_code')
                        ->whereRaw(
                                'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                        );
                    })
                    ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                        $join->on('wl.product_id', '=', 'cp.product_id')
                        ->on("cp.actioned", DB::raw("'0'"))
                        ->on("cp.group", DB::raw("'$group'"));
                    })
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->where('wl.list_type', '=', $group)
                    ->where('wl.is_deleted', 0)
                    ->where('wl.day', $day)
                    ->where('wl.status', '=', 0)
                    //->where('wl.as_of_date', $today)
                    ->count();
        }
        
        

        $cpdata['products'] = $products;
        $cpdata['count'] = $pcount;
        return $cpdata;
    }
    
    
    public static function getUndecostlines($page, $sortcolumn, $sorder)
    {

        $cpdata = $products = $pcount = [];
        $today = date("Y-m-d");
//        $today = '2024-01-17';
        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sorder == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

       
             $group = 7;

            $products = DB::table('dbo.undercost_lines as ul')
                            ->leftjoin("DwProduct as p", "p.Product_Code", "=", "ul.product_code")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                 $join->on('cp.product_id', '=', 'p.Product_Id')
                                ->on('ul.product_code', '=', 'p.Product_code')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('ul.gprice_id as watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "TrueCost"'),
                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "AvgCost"'),
                                    'i.Average_usage as AvgVol',
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                    DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                    DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                    DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                    DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                    DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                     DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                     DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                                      DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),
                                    'cp.phoenix_outofstock',
                                    'cp.trident_outofstock',
                                    'cp.aah_outofstock',
                                    'cp.colorama_outofstock',
                                    'cp.bestway_outofstock',
                                    'ul.asofdate as AsOfDate'
                            )
                           
                            ->where('ul.asofdate', $today)
                            ->where('g.actioned', 0)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
                $products->sensitivity = 'No';
                $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock];
                $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock];
                $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock];
                $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock];
                $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock];
                if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                    $products->sensitivity = 'Yes';
                }
                return $products;
            });

            $pcount = DB::table('dbo.undercost_lines as ul')
                            ->leftjoin("DwProduct as p", "p.Product_Code", "=", "ul.product_code")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                                 $join->on('cp.product_id', '=', 'p.Product_Id')
                                ->on('ul.product_code', '=', 'p.Product_code')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$group'"));
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                   ->where('ul.asofdate', $today)
                    ->count();
        
        
        

        $cpdata['products'] = $products;
        $cpdata['count'] = $pcount;
        return $cpdata;
    }
    
    
    public static function getUndecostlinesWatchlist($page, $sortcolumn, $sorder)
    {

        $cpdata = $products = $pcount = [];
        $today = date("Y-m-d");
//        $today = '2024-01-17';
        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sorder == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

       
             $group = 7;

            $products = DB::table('dbo.undercost_lines as ul')
                            ->leftjoin("DwProduct as p", "p.Product_Code", "=", "ul.product_code")
                       
                            ->select('ul.gprice_id as watchlist_id', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                   'ul.asofdate as AsOfDate'
                            )
                           
                            ->where('ul.asofdate', $today)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = self::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = self::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;
              
                return $products;
            });

            $pcount = DB::table('dbo.undercost_lines as ul')
                            ->leftjoin("DwProduct as p", "p.Product_Code", "=", "ul.product_code")
                           
                   ->where('ul.asofdate', $today)
                    ->count();
        
        
        

        $cpdata['products'] = $products;
        $cpdata['count'] = $pcount;
        return $cpdata;
    }


    public static function getCompetitorPricingDataRDS($group, $page, $sortcolumn, $sorder)
    {

        $cpdata = [];
        $today = date("Y-m-d");

        $limit = 20;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sorder == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $products = DB::table('dbo.product_watchlist as wl')
                ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                    $join->on('wl.product_id', '=', 'cp.product_id')
                    ->on("cp.actioned", DB::raw("'0'"))
                    ->on("cp.group", DB::raw("'$group'"));
                })
                ->leftjoin('group_pricing as g', function ($join) {
                    $join->on('p.Product_Code', '=', 'g.product_code')
                    ->whereRaw(
                            'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                    );
                })
                ->select('wl.watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                
                    DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS phoenix"),
                    DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS trident"),
                    DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS aah"),
                    DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS colorama"),
                    DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS bestway"),

                        'phoenix_outofstock',
                        'trident_outofstock',
                        'aah_outofstock',
                        'colorama_outofstock',
                        'bestway_outofstock',
                        'wl.as_of_date as AsOfDate',
                        'cp.group'
                )->where('wl.list_type', '=', $group)
                ->where('wl.as_of_date', $today)
                ->where('wl.status', '=', 0)
                ->where('wl.is_deleted', 0)
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get()
                ->map(function ($products) {
            $products->sensitivity = 'No';
            $products->phoenix = ['price' => $products->phoenix, 'outofstock' => $products->phoenix_outofstock];
            $products->trident = ['price' => $products->trident, 'outofstock' => $products->trident_outofstock];
            $products->aah = ['price' => $products->aah, 'outofstock' => $products->aah_outofstock];
            $products->colorama = ['price' => $products->colorama, 'outofstock' => $products->colorama_outofstock];
            $products->bestway = ['price' => $products->bestway, 'outofstock' => $products->bestway_outofstock];
            if (($products->phoenix_outofstock + $products->trident_outofstock + $products->aah_outofstock + $products->colorama_outofstock + $products->bestway_outofstock) > 1) {
                $products->sensitivity = 'Yes';
            }

            return $products;
        });
        $pcount = DB::table('dbo.product_watchlist as wl')
                        ->leftjoin("DwProduct as p", "p.Product_Id", "=", "wl.product_id")
                        ->leftjoin('competitor_prices as cp', function ($join) use ($group) {
                            $join->on('wl.product_id', '=', 'cp.product_id')
                            ->on("cp.actioned", DB::raw("'0'"))
                            ->on("cp.group", DB::raw("'$group'"));
                        })
                        ->leftjoin('group_pricing as g', function ($join) {
                            $join->on('p.Product_Code', '=', 'g.product_code')
                            ->whereRaw(
                                    'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                            );
                        })
                        ->where('wl.list_type', '=', $group)
                        ->where('wl.status', '=', 0)
                        ->where('wl.as_of_date', $today)
                        ->where('wl.is_deleted', 0)->count();

        $cpdata['products'] = $products;
        $cpdata['count'] = $pcount;
        return $cpdata;
    }
    
    
    

    private function uniqueAssocArray($array, $uniqueKey)
    {
        $unique = array();
        foreach ($array as $value) {
            $unique[$value[$uniqueKey]] = $value;
        }
        $data = array_values($unique);
        return $data;
    }

    public static function getSupplierComment($ac4)
    {
//         $fday = date('2021-01-01');
        $today = date("Y-m-d");
        $fday = date('Y-m-d', strtotime('-20 day', strtotime($today)));

        $suppliersComments = PricingSupplierPriceData::join('suppliers as s', 's.id', '=', 'pricing_data.supplier_id')
//                        ->select(DB::raw("CONCAT(s.code, ' ', pricing_data.product_code) AS full_name"), DB::raw(" AS formatted_date"))
                        ->select(DB::raw("CONCAT(s.code, '-', pricing_data.product_code,'-',pricing_data.comments,'- added on ',FORMAT(pricing_data.price_from_date, 'dd.MM.yy')) AS cment"), 'pricing_data.product_code')
                        ->where('pricing_data.parent_product_code', $ac4)
                        ->where('pricing_data.source_id', 10)
                        ->where('pricing_data.comments', 'not like', '')
                        ->orderBy('pricing_data.price_from_date', 'DESC')
                        ->where('pricing_data.price_from_date', '>=', $fday)->where('pricing_data.price_from_date', '<=', $today)
                        ->get()->toArray();

//        $suppliersComments = self::uniqueAssocArray($suppliersComments, 'scment');
        $suppliersComments = array_column($suppliersComments, 'cment');
        $scommentsStr = implode(', ', $suppliersComments);

        return $scommentsStr;
    }

    public static function getBuyerComment($productid)
    {

        $today = date("Y-m-d");
        $commentsStr = '';
        $comments = Comment::join('supplier_product_comments as spc', 'spc.comment_id', '=', 'comments.comment_id')
                        ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'spc.product_id')
//                        ->join('pricing_data as pc', 'pc.id', '=', 'spc.pc_id')
//                        ->join('suppliers as s', 's.id', '=', 'pc.supplier_id')
                        ->where('spc.product_id', $productid)
                         ->orderBy('spc.created_at', 'DESC')
//                        ->whereDate('spc.created_at', '=', $today)
//                        ->select(DB::raw("CONCAT(s.code, '-', pc.product_code,'-',comments.title) AS bcment"))->get()->toArray();
                        ->select(DB::raw("REPLACE(comments.title, '-', '') AS bcment"))->limit(1)->get()->toArray();

        $comments = array_column($comments, 'bcment');

        $commentsStr = implode(', ', $comments);

        return $commentsStr;
    }

    public static function getPricierComment($productid)
    {

        $today = date("Y-m-d");
        $fday = date('Y-m-d', strtotime('-6 day', strtotime($today)));
        $commentsStr = '';
        $comments = Comment::join('pricier_comments as pc', 'pc.comment_id', '=', 'comments.comment_id')
                        ->join('dbo.DwProduct as p', 'p.Product_Id', '=', 'pc.product_id')
                        ->where('pc.product_id', $productid)
                        ->whereDate('pc.created_at', '>=', $fday)
                        ->select('comments.title AS pcoment')->get()->toArray();

        $comments = array_column($comments, 'pcoment');

        $commentsStr = implode(', ', $comments);

        return $commentsStr;
    }

    public static function getBuyerCommentOld()
    {
        $today = date("Y-m-d");
        $commentsStr = '';
        $comments = Comment::join('supplier_product_comments as spc', 'spc.comment_id', '=', 'comments.comment_id')
                        ->join('pricing_data as pc', 'pc.id', '=', 'spc.pc_id')
                        ->join('suppliers as s', 's.id', '=', 'pc.supplier_id')
//                    ->where('spc.product_id', $productid)
                        ->whereDate('spc.created_at', '=', $today)
                        ->select('spc.product_id', 'comments.title', 's.code', 'pc.product_code')->get()->toArray();

        $commentsStrArr = [];

        foreach ($comments as $comment) {
            if (!empty($comment['title'])) {

                $commentsStrArr[] = $comment['code'] . " - " . $comment['product_code'] . " - " . $comment['title'];
            }
        }

        $commentsStr = implode(', ', $commentsStrArr);
        return $commentsStrArr;
    }

    public static function getWatchlistType($type)
    {
        $listType = '';

        switch ($type) {
            case 1:
                $listType = 'Buyer Intel';
                break;

            case 2:
                $listType = 'PRESET FOR OFFICE-DAILY';
                break;
            case 3:
                $listType = 'RDS';
                break;

            case 4:
                $listType = 'Buyer Watchlist';
                break;
            case 5:
                $listType = 'Offers';
                break;
            case 6:
                $listType = 'PRESET FOR OFFICE - TWICE A WEEK';
                break;
            default:
                break;
        }
        return $listType;
    }

    /**
     * Get List of all SPOT Codes
     *
     *
     * @return \Illuminate\Http\Response
     */
    public static function getCompetitors()
    {
        $competitors = [];
        $competitors = DB::table('competitors')
                ->select('competitor_id as id', 'name')
                ->where('is_deleted', '=', 0)
                ->orderBy('competitor_id', 'ASC')
                ->get();
        return $competitors;
    }

    /**
     * Get getACProduct
     *
     *
     * @return \Illuminate\Http\Response
     */
    public static function getACProduct($prodId)
    {
        $ac4 = '';
        $ac4 = DB::table('products')
                        ->where(["prod_id" => $prodId])
                        ->pluck("ac4")->first();
        return $ac4;
    }

    public static function getPropertyValueById($id, $propertyName)
    {
        // Retrieve the user instance by ID
        $model = self::find($id);

        // If the user is found, return the value of the specified property
        if ($model) {
            return $model->{$propertyName};
        }

        // Return null if the user is not found
        return null;
    }
    
    
    /*
     * Gets latest product trends
     * 
     */

    public function getLatesProductTrend()
    {
       
            $trends = DB::table('product_price_indicator as pi')
                         ->join('products as pp', 'pp.prod_id', '=', 'pi.product_id')
                   ->join('users as u', 'u.id', '=', 'pi.inserted_by')
                    ->where('u.is_deleted', '=', 0)->whereNull("pi.end_date")->whereIn("pi.price_indicator", [1,2,3])
                    ->select('pp.prod_id as productpage_id',   'pp.ac4 as ac4', 'pi.price_indicator as trendid',
                            \DB::raw("(CASE 
                        WHEN pi.price_indicator = '1' THEN 'Up'
                        WHEN pi.price_indicator = '2' THEN 'Down'
                        WHEN pi.price_indicator = '3' THEN 'Steady'
                        ELSE ' - ' 
                       END) AS trend"),
//                       DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"), 
                               'u.firstname AS created_by', 
                            'pi.created_at as created_on',
                           )
                     ->orderBy("pi.created_at", "DESC")
                    ->limit(10)
                    ->get();

            return $trends;
        
    }
    
      /*
     * Gets Comments
     * 
     */

    public function getTrends($page, $sortcolumn, $sort)
    {
        $limit = 10;
        $page = (int) $page;
        $sort = (int) $sort;

        $sorting = "DESC";
        if ($sort == 1) {
            $sorting = "ASC";
        }

          if ($sortcolumn == 'created_at') {
            $sortcolumn = 'pi.created_at';
        }
            $trends = DB::table('product_price_indicator as pi')
                         ->join('products as pp', 'pp.prod_id', '=', 'pi.product_id')
                   ->join('users as u', 'u.id', '=', 'pi.inserted_by')
                    ->where('u.is_deleted', '=', 0)->whereNull("pi.end_date")->whereIn("pi.price_indicator", [1,2,3])
                    ->select('pp.prod_id as productpage_id',   'pp.ac4 as ac4', 'pi.price_indicator as trendid',
                            \DB::raw("(CASE 
                        WHEN pi.price_indicator = '1' THEN 'Up'
                        WHEN pi.price_indicator = '2' THEN 'Down'
                        WHEN pi.price_indicator = '3' THEN 'Steady'
                        ELSE ' - ' 
                       END) AS trend"),
                       DB::raw("CONCAT(u.firstname,' ',u.lastname) AS created_by"),
//                            'u.firstname AS created_by', 
                            'pi.created_at as created_on',
                           )
                     ->orderBy($sortcolumn, $sorting)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            return $trends;
        
    }
    
    
     /*
     * Gets Comments
     * 
     */

    public function getTrendsCount()
    {
            $trendCount = 0;
            $trendCount = DB::table('product_price_indicator as pi')
                         ->join('products as pp', 'pp.prod_id', '=', 'pi.product_id')
                   ->join('users as u', 'u.id', '=', 'pi.inserted_by')
                    ->where('u.is_deleted', '=', 0)->whereNull("pi.end_date")->whereIn("pi.price_indicator", [1,2,3])
                    ->count();

            return $trendCount;
        
    }
    

}