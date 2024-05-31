<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\DwProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use Auth;
use App\Models\PricingSupplierPriceData;
use App\Models\ProductTags;
use App\Models\ProductBackground;
use App\Models\ProductTaskUser;
use Illuminate\Support\Facades\Validator;
use App\Models\CompetitorPricing;
use App\Models\Tag;
use App\Models\SupplierProductComments;
use App\Models\PricierComments;
use App\Models\Comment;
use App\Models\GroupPricing;
use App\Services\ActivityLogService;
use Carbon\Carbon;

class ProductController extends BaseController
{

    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function index($page, $sortcolumn, $sort)
    {

        $pdata = [];
        $sortcolumn;
        $limit = 30;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'ac4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        if ($sortcolumn == 'parent_product_code') {
            $sortcolumn = 'ac4';
        } else if ($sortcolumn == 'clean_description') {
            $sortcolumn = DB::raw('CAST(clean_description AS NVARCHAR(500))');
        } if ($sortcolumn == 'dt_pack') {
            $sortcolumn = 'pack_size';
        }


        $rowCnt = Product::where('status', '=', 'Live')
                ->count();

        $products = Product::select('prod_id', 'ac4 as parent_product_code', 'product_code', 'clean_description', 'pack_size as dt_pack', 'dt_type', DB::raw('CAST(dt_price AS DECIMAL(10,2)) AS dt_price'), 'status')->where('status', '=', 'Live')
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        $pdata["ac4"] = Product::select('ac4 as parent_product_code')->where('is_parent', '=', 1)->where('status', '=', 'Live')->where('ac4', 'not like', '%***%')->orderBy('ac4', 'ASC')->get();

        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Products retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of discontinued products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function discontinued($page, $sortcolumn, $sort)
    {

        $pdata = [];
        $sortcolumn;
        $limit = 30;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'ac4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        if ($sortcolumn == 'parent_product_code') {
            $sortcolumn = 'ac4';
        } else if ($sortcolumn == 'clean_description') {
            $sortcolumn = DB::raw('CAST(clean_description AS NVARCHAR(500))');
        } if ($sortcolumn == 'dt_pack') {
            $sortcolumn = 'pack_size';
        }


        $rowCnt = Product::where('status', '=', 'Discontinued')
                ->count();

        $products = Product::select('prod_id', 'ac4 as parent_product_code', 'product_code', 'clean_description', 'pack_size as dt_pack', 'dt_type', DB::raw('CAST(dt_price AS DECIMAL(10,2)) AS dt_price'), 'status')->where('status', '=', 'Discontinued')
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        $pdata["ac4"] = Product::select('ac4 as parent_product_code')->where('is_parent', '=', 1)->where('status', '=', 'Live')->where('ac4', 'not like', '%***%')->orderBy('ac4', 'ASC')->get();

        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Products retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of discontinued products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function new($page, $sortcolumn, $sort)
    {
        $pdata = [];
        $sortcolumn;
        $limit = 2;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'ac4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        if ($sortcolumn == 'parent_product_code') {
            $sortcolumn = 'ac4';
        } else if ($sortcolumn == 'clean_description') {
            $sortcolumn = DB::raw('CAST(clean_description AS NVARCHAR(500))');
        } if ($sortcolumn == 'dt_pack') {
            $sortcolumn = 'pack_size';
        }


        $rowCnt = Product::where('prod_status', '=', Product::INCOMPLETE)
                ->count();

        $products = Product::select('prod_id', 'ac4 as parent_product_code', 'product_code', 'clean_description', 'pack_size as dt_pack', 'dt_type', DB::raw('CAST(dt_price AS DECIMAL(10,2)) AS dt_price'), 'status')->where('prod_status', '=', Product::INCOMPLETE)
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        $pdata["ac4"] = Product::select('ac4 as parent_product_code')->where('is_parent', '=', 1)->where('status', '=', 'Live')->where('ac4', 'not like', '%***%')->orderBy('ac4', 'ASC')->get();

        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'New products retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of product items
     *
     * @return json The product Items json array
     */
    public function showall()
    {
        $products = Product::select('prod_id', 'ac4 as parent_product_code', 'product_code', 'clean_description', 'dt_pack', 'dt_type', DB::raw('CAST(dt_price AS DECIMAL(10,2)) AS dt_price'))->orderBy('dt_price', 'DESC')->limit(100)->get();
        if (!empty($products)) {
            return response()->json($products);
        } else {
            return response()->json([
                        'success' => false,
                        'message' => "No any product data found",
                        "data" => $products
                            ], 404);
        }
    }

    /**
     * API to search product
     * Search product by keyword of parent product code or clean description OR
     * description keyword space in space dt pack value
     * Example: ac in 100 should give results like "Aciclovir 400mg/5ml oral suspension sugar free 100 ml  ACIS41"
     * As the product description contains "ac" keyword and it has dt pack value equal to 100 OR
     * Using description combination : keyword space keyword space keyword and go on
     * Eg.ac 33 ga should search product having description "Acamprosate 333mg gastro-resistant tablets 168 tablet"
     *
     * @param string $term The search Parameter
     *
     * @return json The Json Array of parent product items
     */
    public function searchProduct($keyword = '')
    {
        $output = "";
        $products = $productsA = array();

        if (!empty($keyword)) {
            $products = Product::searchProduct($keyword);
        }


        if (!empty($products)) {
            return $this->sendResponse(array_values($products), 'Products retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product data found']);
        }
    }

    /**
     * Gets the product details like product parent code. description
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page data
     */
    public function getProductHeader($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['product_page_header'] = Product::getProductHeader($productid);
        $data['price_indicator'] = Product::getPriceIndicator($productid);
        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page header details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product header details found']);
        }
    }

    /**
     * Gets the product active and historical tag details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page tag data
     */
    public function getProductTagsDetails($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['tags'] = Product::getTagDetails($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page tag details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product tag details found']);
        }
    }

    /**
     * Gets the product page inventory details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page inventory data
     */
    public function getProductInventoryDetails($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['inventory'] = Product::getInventoryData($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page inventory details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product inventory details found']);
        }
    }
    
    
    /**
     * Gets the product ARIVolume
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page inventory data
     */
    public function getProductARIVolume($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data = Product::getProductARIVolume($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product ARI Volume retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'Product ARI volume is not found']);
        }
    }

    /**
     * Gets the product page comments details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page comments data
     */
    public function getProductCommentsDetails($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['comments'] = (new ProductBackground())->getCommetsDetails($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page latest comments retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product comments details found']);
        }
    }

    /**
     * Gets the product page comments details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page comments data
     */
    public function getProductLatestBackground()
    {

        $data = [];

        $data['background'] = (new ProductBackground())->getLatestBackground();

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page latest background information retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product background details found']);
        }
    }

    /**
     * Gets Product page usage data
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page usage data
     */
    public function getOverviewData($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['overiview'] = Product::getUsageData($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page usage details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product usage details found']);
        }
    }

    public function getSigUsage($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['usage'] = Product::getSigmaUsageData($productid);
        $data['addition'] = Product::getSigmaUsageDataAddtn($productid);
        $data['percentage'] = Product::getSigmaUsagePercent($productid);
        $data['pca'] = Product::getLatestPCA($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Sigma Product page usage details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Sigma Product usage details found']);
        }
    }

    public function getSalesVolumeData($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['usage'] = Product::getSigmaSalesVolumeData($productid);
        $data['addition'] = Product::getSigmaSalesVolumeDataSum($productid);
        $data['percentage'] = Product::getSalesVolumneDataPercent($productid);
        $data['pca'] = Product::getLatestPCA($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Sigma Product page usage details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Sigma Product usage details found']);
        }
    }

    /**
     * Gets pricing details for different sources like Supplier Pricing, DT, Price Concession, Wavedata,
     * Competitor Pricing, Telesales and Competitor Offers
     *
     * @param  integer $productid The Product ID
     * @param  integer $type The Source Type
     * @param  integer $page The Page Number
     * @param  integer $sortcolumn The Sort column name
     * @param  integer $sort The Sort order flag (1/0) ASC|DESC
     *
     * @return json The Json array of Product pricing data
     */
    public function getPricingData($productid, $type, $page, $sortcolumn, $sort)
    {
        $productid = (int) $productid;
        $data = [];

        $data['price_capture'] = Product::getPriceCaptureData($productid, $type, $page, $sortcolumn, $sort);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page pricing details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product pricing details found']);
        }
    }

    public function searchPricingData(Request $request, $productid, $type, $page, $sortcolumn, $sort)
    {

        $fday = date('Y-m-01');
//        $fday = '2024-01-01';
        $pdata = [];
        $sortcolumn;
        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        $pcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";
        $requestData = $request->all();
        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";
        
        $scode = !empty($requestData['scode']) && isset($requestData['scode']) ? strtolower($requestData['scode']) : "";
        $today = date("Y-m-d");
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


        $pricingdata = $current_historical = $pricing_current_data = $pricing_historical_data = [];
        $historicalCnt = $currendataCnt = 0;
        $product = Product::where(["prod_id" => $productid])->select("products.ac4 as parent_product_code")->first();
        $productParentCode = !empty($product->parent_product_code) && isset($product->parent_product_code) ? $product->parent_product_code : '';
        $productIds = DB::table('dbo.DwProduct as dp')
                        ->join('products as p', 'p.ac4', '=', 'dp.Product_AC_4')
                        ->where("p.prod_id", $productid)->select("dp.Product_Id")->get()->toArray();
        $productIds = array_column($productIds, "Product_Id");
        //Price Capture Pricing data
        $sourceName = 'Supplier Pricing';
        $sourceId = 10;
        
        $max = PricingSupplierPriceData::where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => 10])->max('price_from_date');
        $previousPDate = date('Y-m-d', strtotime($max . ' -12 months'));

        $maxd = PricingSupplierPriceData::selectRaw("MAX(FORMAT(price_from_date, 'yyyy-MM')) as max_year_month")->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => 10])->value('max_year_month');

        $pricing_current_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id",
                        "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid",
                        "sources.name as source", "suppliers.code as supp_code", "product_code",
                        DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                        DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '0.00', '0'),'00','')  AS negotiated_price"),
                        "forecast", "price_from_date", "price_untill_date", "pricing_data.comments",  \DB::raw("(CASE 
                        WHEN import_type = '1' THEN 'Form Input'
                        WHEN import_type = '2' THEN 'Supplier File' 
                        ELSE 'Manual' 
                       END) AS datasource"))
                ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') = ?", [$maxd])
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get(); //pricing current data price untill date is greater than todays date 

//        $currendataCnt = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
//                ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
//                ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
//                ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') = ?", [$maxd])
//                ->count();

        $currentallData = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                ->select(DB::raw("$productid AS productid"), "pricing_data.id as pricing_id",
                        "pricing_data.supplier_id as supplierid", "pricing_data.source_id as sourceid",
                        "sources.name as source", "suppliers.code as supp_code", "product_code",
                        DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS price"),
                        DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '0.00', '0'),'00','')  AS negotiated_price"),
                        "forecast", "price_from_date", "price_untill_date", "pricing_data.comments")
                ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                ->whereRaw("FORMAT(price_from_date, 'yyyy-MM') = ?", [$maxd])
                ->get();

        $pricing = [];
        $trend = "";

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
            $currendataCnt = count($pricing_current_data);

        if (empty($scode) && !empty($sdate)) {

            $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->select(DB::raw("$productid AS productid"), "pricing_data.supplier_id as supplierid",
                            "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code",
                            "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS price"),
                            DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '.00', '0'),'00','')  AS negotiated_price"),
                            "forecast", "price_from_date", "price_untill_date", "pricing_data.comments",  \DB::raw("(CASE 
                        WHEN import_type = '1' THEN 'Form Input'
                        WHEN import_type = '2' THEN 'Supplier File' 
                        ELSE 'Manual' 
                       END) AS datasource"))
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])
                    ->where('price_from_date', '>=', $sdate)->where('price_from_date', '<=', $edate)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])
                    ->where('price_from_date', '>=', $sdate)->where('price_from_date', '<=', $edate)
                    ->count();
        } else if (!empty($scode) && empty($sdate) && empty($edate)) {
               

            $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->select(DB::raw("$productid AS productid"), "pricing_data.supplier_id as supplierid",
                            "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code",
                            "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS price"),
                            DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '.00', '0'),'00','')  AS negotiated_price"),
                            "forecast", "price_from_date", "price_untill_date", "pricing_data.comments", \DB::raw("(CASE 
                        WHEN import_type = '1' THEN 'Form Input'
                        WHEN import_type = '2' THEN 'Supplier File' 
                        ELSE 'Manual' 
                       END) AS datasource"))
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])
                      ->where(DB::raw("LOWER(suppliers.code)"), 'LIKE', '%' . $scode . '%')
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])
                     ->where(DB::raw("LOWER(suppliers.code)"), 'LIKE', '%' . $scode . '%')
                    ->count();
        } else if (!empty($scode) && !empty($sdate) && !empty($edate)) {


            $pricing_historical_data = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->select(DB::raw("$productid AS productid"), "pricing_data.supplier_id as supplierid",
                            "pricing_data.source_id as sourceid", "sources.name as source", "suppliers.code as supp_code",
                            "product_code",
                            DB::raw("REPLACE(REPLACE(CAST(price AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS price"),
                            DB::raw("REPLACE(REPLACE(CAST(negotiated_price AS DECIMAL(10,2)), '.00', '0'),'00','')  AS negotiated_price"),
                            "forecast", "price_from_date", "price_untill_date", "pricing_data.comments", \DB::raw("(CASE 
                        WHEN import_type = '1' THEN 'Form Input'
                        WHEN import_type = '2' THEN 'Supplier File' 
                        ELSE 'Manual' 
                       END) AS datasource"))
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])
                     ->where(DB::raw("LOWER(suppliers.code)"), 'LIKE', '%' . $scode . '%')
                    ->where('price_from_date', '>=', $sdate)->where('price_from_date', '<=', $edate)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $historicalCnt = PricingSupplierPriceData::join('sources', 'sources.id', '=', 'pricing_data.source_id')
                    ->join('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                    ->where(["pricing_data.parent_product_code" => $productParentCode, "pricing_data.source_id" => $sourceId])
                     ->where(DB::raw("LOWER(suppliers.code)"), 'LIKE', '%' . $scode . '%')
                    ->where('price_from_date', '>=', $sdate)->where('price_from_date', '<=', $edate)
                    ->count();
        }



        $pricingdata = ["source" => 'sp', "source_id" => $sourceId, "currrent" => $pricing_current_data, "historical" => $pricing_historical_data, "min_val" => Product::getCheapestPriceNSupplier($currentallData),
            "current_count" => $currendataCnt, "historical_count" => $historicalCnt
        ];

        return $pricingdata;
    }

    public function getPricingData2($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['price_capture'] = Product::getPriceCaptureData2($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page pricing details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product pricing details found']);
        }
    }

    /**
     * Gets the current month summary for pricing and usage data for respective product
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @return json The Json array of summary for pricing and usage data for respective product
     */
    public function getPricingUsageSummary($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['summary'] = Product::getPricingUsageSummary($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Pricing and usage data summary retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any pricing and usage data summary found']);
        }
    }

    /**
     * Gets 
     * Latest GRN Details
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page data
     */
    public function getGrnData($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['grn'] = Product::getGRNData($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product page GRN details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product GRN details found']);
        }
    }

    /**
     * Gets the product details like product parent code. description
     * Price capture details about usage and prices provided by different suppliers, customers
     * Latest GRN Details
     * Latest Inventory details
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page data
     */
    public function getUsage($productid)
    {
        $productid = (int) $productid;
        $data = [];

        $data['overiview'] = Product::getUsageData($productid);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Product details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product details found']);
        }
    }

    /**
     * Gets the historical price capture details of provided product and source
     * In Product page - Price capture section, when user clicks on source name, all the historical price capture details
     * would be shown with descending order [ latest records displayed first ] in new page
     *
     * @param int $productId The Product ID
     * @param int $sourceId The Source ID
     *
     * @return json The Json array of Price Capture records
     */
    public function getPriceCaptureHistoricalDetails($productId, $sourceId)
    {
        $productId = (int) $productId;
        $data = [];

        $data['header'] = Product::getPriceCaptureHeader($productId, $sourceId);
        $data['price_capture'] = Product::getPriceCaptureHistoricalData($productId, $sourceId);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Price Capture details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Price Capture details found']);
        }
    }

    /**
     * Updates the negotiated price at price cature system from product portal
     *
     * @param \Illuminate\Http\Request $request Request with Aggregate Code, Product Code, Source ID,
     * Supplier Id and negotiated price
     *
     * @return json The Json response of result
     */
    public function updateNegotiatedPrice(Request $request)
    {
        $requestData = $request->all();
        //Gets request data
        $ac4 = !empty($requestData['ac4']) && isset($requestData['ac4']) ? trim($requestData['ac4']) : "";
        $product_code = !empty($requestData['product_code']) && isset($requestData['product_code']) ? trim($requestData['product_code']) : "";
        $source_id = !empty($requestData['source_id']) && isset($requestData['source_id']) ? (int) trim($requestData['source_id']) : "";
        $supplier_id = !empty($requestData['supplier_id']) && isset($requestData['supplier_id']) ? (int) trim($requestData['supplier_id']) : "";
        $negotiated_price = !empty($requestData['negotiated_price']) && isset($requestData['negotiated_price']) ? trim($requestData['negotiated_price']) : "";
        $pricing_id = !empty($requestData['pricing_id']) && isset($requestData['pricing_id']) ? trim($requestData['pricing_id']) : "";
        $pricing_id = !empty($requestData['pricing_id']) && isset($requestData['pricing_id']) ? trim($requestData['pricing_id']) : "";

        $errors = [];
        $errors = Product::validateDataForNegotiatedPrice($ac4, $product_code, $source_id, $supplier_id, $negotiated_price);
        $currentDateTime = Carbon::now();
        //Today
        $currentDate = date('Y-m-d');
        //Yesterday
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
        $inserted_by = $lastchanged_by = Auth::user()->id;

        if (!empty($errors)) {
            $strErrors = implode(", ", $errors);
            return $this->sendErrorResponse('Failed to update negotiated price', $strErrors, 208);
        }

        try {
            //Gets the Price Untill Date of exiting record
            /* $extPricingItem = DB::table('pricing_data')
              ->where(["parent_product_code" => $ac4, "product_code" => $product_code, "source_id" => $source_id, "supplier_id" => $supplier_id
              //, "forecast" => $forecast, "comments" => $comments
              ])->orderBy("price_untill_date", "DESC")->select("id", "price_from_date", "price_untill_date")->first(); */


            $extPricingItem = DB::table('pricing_data')
                            ->where(["id" => $pricing_id])->select("id", "price_from_date", "price_untill_date", "forecast", "comments")->first();

            $extPrice_from_date = !empty($extPricingItem) && isset($extPricingItem->price_from_date) ? trim($extPricingItem->price_from_date) : '';
            $extPrice_until_date = !empty($extPricingItem) && isset($extPricingItem->price_untill_date) ? trim($extPricingItem->price_untill_date) : '';

            if ($extPrice_from_date > $currentDate) {

                return $this->sendErrorResponse('Failed to update negotiated price', 'You can not update future price. Its price from date is grater than current date', 208);
            }


            $forecast = !empty($extPricingItem) && isset($extPricingItem->forecast) ? (int) trim($extPricingItem->forecast) : '';
            $comments = !empty($extPricingItem) && isset($extPricingItem->comments) ? trim($extPricingItem->comments) : '';
            $lastDayofMonth = Carbon::parse($currentDateTime)->endOfMonth()->toDateString();

            //If the price from date of existing record is greater than yersterday, price untill date is sets to today

            if ($yesterday < $extPrice_from_date) {
                $price_untill_date = $currentDate;
            } else {
                $price_untill_date = $yesterday;
            }
            //If the price untill date of existing pricing item is less that current date, price untill date is sets to last day of current month 
            if ($extPrice_until_date < $currentDate) {
                $extPrice_until_date = $lastDayofMonth;
            }

            //Updates the price_untill_date of old/exiting/historical record
            DB::table('pricing_data')
                    ->where(["id" => $extPricingItem->id])
                    ->update(['negotiated_price' => $negotiated_price, 'price_untill_date' => $price_untill_date, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

            //Creates new pricing record with new date range
            PricingSupplierPriceData::create([
                'source_id' => $source_id,
                'supplier_id' => $supplier_id,
                'parent_product_code' => $ac4, // Parent Product Code
                'product_code' => $product_code, //Child Product Code
                'price' => floatval($negotiated_price),
                'forecast' => $forecast,
                'comments' => $comments,
                'price_from_date' => $currentDate,
                'price_untill_date' => $extPrice_until_date,
                'price_type' => PricingSupplierPriceData::PRICE_TYPE_NEGOTIATED_PRICE,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
                'inserted_by' => $inserted_by,
                'lastchanged_by' => $lastchanged_by
            ]);

            return $this->sendResponse('Negotiated Price Updated', 'Negotiated price updated successfully.');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to negotiated price', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * Attach Tag With Product
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function attachTag(Request $request, $productid)
    {
        $productid = (int) $productid;
        $ac4 = Product::getPropertyValueById($productid, 'ac4');

        $requestData = $request->all();

        //Server side validations
        $validator = Validator::make($request->all(), [
                    'tag_id' => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $severity = 1;
        $tag_id = !empty($requestData['tag_id']) && isset($requestData['tag_id']) ? $requestData['tag_id'] : "";
        $severity = !empty($requestData['severity']) && isset($requestData['severity']) && $requestData['severity'] !== 'undefined' ? $requestData['severity'] : 1;
        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();
        $username = Auth::user()->getName();
        $tagname = Tag::getPropertyValueById($tag_id, 'name');
        $formattedDateTime = Carbon::parse($currentDateTime)->format('d-M-Y h:i A');

        //if ($severity == null) {  // bydefault severity low
        //}
        //Check wheather active tag is already mapped to that product or not 
        $productTagExists = ProductTags::where('tag_id', $tag_id)->where('product_id', $productid)->WhereNull('end_date')->first();

        if (!empty($productTagExists)) {

            try {
                //Updates the existing product tag mapping
                $productTagExists->severity = $severity;
                $productTagExists->updated_at = $currentDateTime;
                $productTagExists->lastchanged_by = $lastchanged_by;
                $productTagExists->save();

                /* DB::table('product_tags')
                  ->where(['tag_id' => $tag_id, 'product_id' => $productid])
                  ->update(['severity' => $severity, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]); */
                $activity = 'Updated Tag';
                $description = 'Updated Tag "' . $tagname . '" for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime;
                $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                return $this->sendResponse('Tag Updated Successfully', 200);
            } catch (\Exception $error) {
                $activity = 'Updated Tag Failed';
                $description = 'Failed to update tag "' . $tagname . '" for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime . 'Error -  ' . $error->getMessage();
                $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                return $this->sendErrorResponse('Tag attached to product failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {

            try {
                ProductTags::create([
                    'product_id' => $productid,
                    'tag_id' => $tag_id,
                    'severity' => $severity,
                    'created_at' => $currentDateTime,
                    'inserted_by' => $insertedby,
                    'end_date' => NULL
                ]);
                $activity = 'Added Tag';
                $description = 'Added Tag "' . $tagname . '" for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime;
                $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                return $this->sendResponse('Tag attached to product successfully', 200);
            } catch (\Exception $error) {
                $activity = 'Add Tag Failed';
                $description = 'Failed to add tag "' . $tagname . '" for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime . 'Error -  ' . $error->getMessage();
                $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                return $this->sendErrorResponse('Tag attached to product failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        }
    }

    /**
     * Deactivate tag with product 
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function deactivateTag(Request $request, $productid)
    {

        $productid = (int) $productid;
        $requestData = $request->all();
        $tag_id = !empty($requestData['tag_id']) && isset($requestData['tag_id']) ? $requestData['tag_id'] : "";
        $id = !empty($requestData['id']) && isset($requestData['id']) ? $requestData['id'] : "";
        $severity = !empty($requestData['severity']) && isset($requestData['severity']) ? $requestData['severity'] : "";
        $currentDateTime = Carbon::now();

        $lastchanged_by = Auth::user()->id;
        //We can delete tag using date we can add date in end_date after adding end_date tag is going to historical state
        $ac4 = Product::getPropertyValueById($productid, 'ac4');
        $username = Auth::user()->getName();
        $tagname = Tag::getPropertyValueById($tag_id, 'name');
        $formattedDateTime = Carbon::parse($currentDateTime)->format('d-M-Y h:i A');
        try {
            DB::table('product_tags')
                    ->where(["product_id" => $productid, "tag_id" => $tag_id, "severity" => $severity, "id" => $id])
                    ->update(['end_date' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
            $activity = 'Remove Tag';
            $description = 'Removed Tag "' . $tagname . '" for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime;
            $this->activityLogService->logActivity($lastchanged_by, $activity, $description, $request);

            return $this->sendResponse('Tag is unlinked successfully ', 200);
        } catch (\Exception $error) {
            $activity = 'Remove Tag Failed';
            $description = 'Failed to remove tag "' . $tagname . '" for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime . 'Error -  ' . $error->getMessage();
            $this->activityLogService->logActivity($lastchanged_by, $activity, $description, $request);
            return $this->sendErrorResponse('Tag unlinking failed ', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * Tag With All products 
     *
     * @param  \App\Product  $tag_id The Tag ID
     *
     * @request tag_id 
     * 
     * @return json The JSON array to send response
     */
    public function tagWithAllProducts($tag_id)
    {

        $tag_id = (int) $tag_id;

        $product_list = DB::table('products as p')
                ->join("product_tags as pt", "p.prod_id", "=", "pt.product_id")
                ->select('p.prod_id', 'p.ac4', 'pt.created_at', 'p.clean_description')
                ->where('pt.tag_id', '=', $tag_id)
                ->whereNull('pt.end_date')
                ->get();

        $tag = DB::table('tags')->select('tag_id', 'name')->where('tag_id', $tag_id)->get(); //get tag name 


        $tag_products = ["tag" => $tag, "products" => $product_list]; //return tag name and product names,etc.

        return $tag_products;
    }

    /**
     * Add comment/Task
     *
     * @param Request $request
     * @param int $productid
     * @return type
     */
    public function addComment(Request $request, $productid)
    {
        $productid = (int) $productid;
        $requestData = $request->all();
        $category = !empty($requestData['category']) && isset($requestData['category']) ? $requestData['category'] : "";
        $roleIds = !empty($requestData['role_id']) && isset($requestData['role_id']) ? $requestData['role_id'] : "";
        $userIds = !empty($requestData['user_id']) && isset($requestData['user_id']) ? $requestData['user_id'] : "";
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? $requestData['comment'] : "";

        $status = 0;
        $currentDateTime = Carbon::now();
        $ac4Code = Product::where(["prod_id" => $productid])->pluck("products.ac4")->first();
        $insertedby = $lastchanged_by = Auth::user()->id;
        $isTask = 0;
        $usersTaskArr = [];

        if ((!empty($userIds) && $userIds != null) || (!empty($roleIds) && $roleIds != null)) {
            $category = ProductBackground::CATEGORY_TASK;
        }
        if (!empty($userIds) && $userIds != null) {
            $message = 'Task assigned to users successfully';
        } elseif (!empty($roleIds) && $roleIds != null) {
            $message = 'Task assigned to user group successfully';
        } elseif ($category == ProductBackground::CATEGORY_BACKGROUND) {
            $message = 'Background information added successfully';
        } else {
            $message = 'Comment created successfully';
        }

        try {
            //Create Comment/Task
            $commentObj = ProductBackground::create([
                        'comment' => $comment,
                        'product_id' => $productid,
                        'created_at' => $currentDateTime,
                        'inserted_by' => $insertedby,
                        'category' => $category
            ]);

            $taskId = !empty($commentObj) ? $commentObj->bg_comment_task_id : NULL;
            //When any comment is assigned to user or user group it is considered as Task
            //
            //Task For User
            if ((empty($roleIds) || $roleIds == null) && (!empty($userIds) || $userIds != null)) {

                foreach ($userIds as $userId) {
                    $usersTask[] = [
                        'product_id' => $productid,
                        'task_id' => $taskId,
                        'user_id' => $userId,
                        'created_at' => $currentDateTime,
                        'inserted_by' => $insertedby
                    ];
                }


                ProductTaskUser::insert($usersTask);

                //Sends email notification of assigned task to user/s
                ProductBackground::sendTaskNotification($userIds, $ac4Code, $comment);
            }
            //Task For User Group / Role

            /* if ((!empty($roleIds) || $roleIds != null) && (empty($userIds) || $userIds == null)) {
              $roleIds = array_values($roleIds);
              //Get all users with provided role ids
              $user_with_this_role = DB::table('users as u')
              ->select("u.id")
              ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
              ->whereIn('mhr.role_id', $roleIds)
              ->where("u.is_deleted", 0)
              ->get()->pluck('id')->toArray();

              foreach ($roleIds as $roleId) {
              $usersRTask[] = [
              'product_id' => $productid,
              'comment_id' => $taskId,
              'role_id' => $roleId,
              'created_at' => $currentDateTime,
              'inserted_by' => $insertedby,
              ];
              }

              //Bulk insert of product comment role items
              ProductCommentRole::insert($usersRTask);

              foreach ($user_with_this_role as $uId) {
              $usersTaskArr[] = [
              'product_id' => $productid,
              'task_id' => $taskId,
              'user_id' => $uId,
              'created_at' => $currentDateTime,
              'inserted_by' => $insertedby
              ];
              }


              ProductTaskUser::insert($usersTaskArr);

              //Sends email notification of assigned task to user/s of provided role/s
              ProductBackground::sendTaskNotification($user_with_this_role, $ac4Code, $comment);

              } */

            return $this->sendResponse($message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Comment/Task creation failed', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    public function createBackground($info, $productid, $currentDateTime, $insertedby, $category)
    {
        
    }

    /**
     * Add comment/Task/Background
     *
     * @param Request $request
     * @param int $productid
     * @return void
     */
    public function addCommentWatchlistOld(Request $request, $productid)
    {
        $productid = (int) $productid;
         $ac4 = Product::getPropertyValueById($productid, 'ac4');
        $requestData = $request->all();
       
        $type = !empty($requestData['type']) && isset($requestData['type']) ? $requestData['type'] : "";
        $userIds = !empty($requestData['user_id']) && isset($requestData['user_id']) ? $requestData['user_id'] : "";
        //Free-text comment
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? $requestData['comment'] : "";
        //Predefined comment
        $commentRId = !empty($requestData['comment_id']) && isset($requestData['comment_id']) ? $requestData['comment_id'] : "";
        $spotProductId = !empty($requestData['product']) && isset($requestData['product']) ? $requestData['product'] : "";
        $watchlist = !empty($requestData['watchlist']) && isset($requestData['watchlist']) ? $requestData['watchlist'] : "";
        $asofdate = date("Y-m-d");
        $currentDateTime = Carbon::now();
        $ac4Code = Product::where(["prod_id" => $productid])->pluck("products.ac4")->first();
        $spotCode = DB::table('dbo.DwProduct')->where("Product_Id", $spotProductId)->pluck("Product_Code")->first();
        $insertedby = $lastchanged_by = Auth::user()->id;
        $username = Auth::user()->getName();
        $formattedDateTime = Carbon::parse($currentDateTime)->format('d-M-Y h:i A');
        try {

            //Background
            if ($type == 1) {



                $bg = ProductBackground::where('info', 'like', $comment)->where('is_deleted', 0)->select('product_background_id')->first();
                if (!empty($bg)) {
                    $bgId = $bg->product_background_id;
                    DB::table('product_background')
                            ->where(['product_background_id' => $bgId])
                            ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                    $message = 'Product background information already available';
                    
                } else {
                    ProductBackground::create([
                        'info' => $comment,
                        'product_id' => $productid,
                        'created_at' => $currentDateTime,
                        'inserted_by' => $insertedby
                    ]);
                    $message = 'Product background information added successfully';
                    
                    $activity = 'Added Product Background';
                    $description = 'Added background information for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime;
                    $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                }
            }
            //Comment and task

            if ($type == 2 || $type == 3) {
                //Comment//Task
                //comments table cgroup 1 means buyer comment, cgroup 2 means pricer comment, cgroup 3 means task
                $cgroup = $type == 2 ? 1 : 3;
                
                if(!empty($commentRId)) {
                    $commentId  = $commentRId;
                    
                } else {
                   $commentObj = Comment::where('title', $comment)->where('is_deleted', 0)->select('comment_id')->first();

                if (!empty($commentObj)) {

                    DB::table('comments')
                            ->where(['comment_id' => $commentObj->comment_id, 'cgroup' => 1])
                            ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                } else {
                    $commentObj = Comment::create([
                                'title' => $comment,
                                'created_at' => $currentDateTime,
                                'inserted_by' => $insertedby,
                                'cgroup' => $cgroup
                    ]);
                }
                   $commentId = !empty($commentObj) ? $commentObj->comment_id : '';
                }

                
             
                $taskId = !empty($commentObj) ? $commentObj->comment_id : NULL;

                //Comment
                if ($type == 2) {



                    //Product comment
                    $productCommentExists = SupplierProductComments::where('comment_id', $commentId)->where('product_id', $spotProductId)->first();
                    if (!empty($productCommentExists)) {
                        $productCommentExists->updated_at = $currentDateTime;
                        $productCommentExists->lastchanged_by = $lastchanged_by;
                        $productCommentExists->save();
                    } else {

                        SupplierProductComments::create([
                            'product_id' => $spotProductId,
                            'pc_id' => NULL,
                            'comment_id' => $commentId,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ]);
                    }
                    //Product Watchlist
                    $exists = DB::table('product_watchlist')->where('product_id', $spotProductId)
                                    ->where('as_of_date', $asofdate)->where('list_type', $watchlist)->where('is_deleted', 0)->first();
                    $watchlistType = !empty($exists->list_type) && isset($exists->list_type) ? Product::getWatchlistType($exists->list_type) : '';
                    $watchlistType = str_replace("Watchlist", "", $watchlistType);

                    if (!empty($exists)) {

                        $msg = '. Product is already available in the ' . $watchlistType . ' watchlist';
                    } else {

                        DB::table('product_watchlist')->insert([
                            'product_id' => $spotProductId,
                            'list_type' => $watchlist,
                            'as_of_date' => $asofdate,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ]);
                        $msg = ' and product is added to watchlist successfully';
                    }

                    $message = 'Comment created successfully' . $msg;
                    $activity = 'Added Comment';
                    $description = 'Added Comment for the product "' . $spotCode . $msg . '" by ' . $username . ' on ' . $formattedDateTime;
                    $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                }


                //Task

                if ($type == 3) {


                    foreach ($userIds as $userId) {
                        $usersTask[] = [
                            'product_id' => $spotProductId,
                            'task_id' => $taskId,
                            'user_id' => $userId,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ];
                    }

                    ProductTaskUser::insert($usersTask);

                    //Sends email notification of assigned task to user/s
                    ProductBackground::sendTaskNotification($userIds, $ac4Code, $spotCode, $comment);
                    $message = 'Task assigned to users successfully';
                    $activity = 'Assigned Task';
                    $description = 'Task assigned to users for the product "' . $spotCode . '" by ' . $username . ' on ' . $formattedDateTime;
                    $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                }
            }
            return $this->sendResponse($message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Comment/Task creation failed', 'Failed : ' . $error->getMessage(), 208);
        }
    }
    
    
    
     /**
     * Add comment/Task/Background
     *
     * @param Request $request
     * @param int $productid
     * @return void
     */
    public function addCommentWatchlist(Request $request, $productid)
    {
        $productid = (int) $productid;
         $ac4 = Product::getPropertyValueById($productid, 'ac4');
        $requestData = $request->all();
       
        $type = !empty($requestData['type']) && isset($requestData['type']) ? $requestData['type'] : "";
        $userIds = !empty($requestData['user_id']) && isset($requestData['user_id']) ? $requestData['user_id'] : "";
        //Free-text comment
        $comment = !empty($requestData['comment']) && isset($requestData['comment']) ? $requestData['comment'] : "";
        //Predefined comment
        $commentRId = !empty($requestData['comment_id']) && isset($requestData['comment_id']) ? $requestData['comment_id'] : "";
        $spotProductId = !empty($requestData['product']) && isset($requestData['product']) ? $requestData['product'] : "";
        $watchlist = !empty($requestData['watchlist']) && isset($requestData['watchlist']) ? $requestData['watchlist'] : "";
        $asofdate = date("Y-m-d");
        $currentDateTime = Carbon::now();
        $ac4Code = Product::where(["prod_id" => $productid])->pluck("products.ac4")->first();
        $spotCode = DB::table('dbo.DwProduct')->where("Product_Id", $spotProductId)->pluck("Product_Code")->first();
        $insertedby = $lastchanged_by = Auth::user()->id;
        $username = Auth::user()->getName();
        $formattedDateTime = Carbon::parse($currentDateTime)->format('d-M-Y h:i A');
        try {
            if(empty($comment) && empty($commentRId)) {
                  return $this->sendErrorResponse('Comment creation failed', 'Failed : Comment is required ', 208);
            }
            //Background
            if ($type == 1) {



                $bg = ProductBackground::where('info', 'like', $comment)->where('is_deleted', 0)->select('product_background_id')->first();
                if (!empty($bg)) {
                    $bgId = $bg->product_background_id;
                    DB::table('product_background')
                            ->where(['product_background_id' => $bgId])
                            ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                    $message = 'Product background information already available';
                    
                } else {
                    ProductBackground::create([
                        'info' => $comment,
                        'product_id' => $productid,
                        'created_at' => $currentDateTime,
                        'inserted_by' => $insertedby
                    ]);
                    $message = 'Product background information added successfully';
                    
                    $activity = 'Added Product Background';
                    $description = 'Added background information for the product "' . $ac4 . '" by ' . $username . ' on ' . $formattedDateTime;
                    $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                }
            }
            //Comment and task

            if ($type == 2 || $type == 3) {
                    $preDefinedComment = '';
                 if(!empty($commentRId)) {
                    $preDefinedComment = Comment::where('comment_id', $commentRId)->pluck('title')->first();
                    $comment =  !empty($comment) ? $preDefinedComment. ', '. $comment : $preDefinedComment;
                 }
                 
                //Comment//Task
                //comments table cgroup 1 means buyer comment, cgroup 2 means pricer comment, cgroup 3 means task
                $cgroup = $type == 2 ? 1 : 3;
                
                if(!empty($comment)) {
                     $commentObj = Comment::where('title', $comment)->where('is_deleted', 0)->select('comment_id')->first();
                     
                     if (!empty($commentObj)) {

                    DB::table('comments')
                            ->where(['comment_id' => $commentObj->comment_id, 'cgroup' => 1])
                            ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                } else {
                    $commentObj = Comment::create([
                                'title' => $comment,
                                'created_at' => $currentDateTime,
                                'inserted_by' => $insertedby,
                                'cgroup' => $cgroup
                    ]);
                }
                $commentId = $taskId = !empty($commentObj) ? $commentObj->comment_id : NULL;
                
                }
                
               
                //Comment
                if ($type == 2) {
                    $freeText = SupplierProductComments::where('comment_id', $commentId)->where('product_id', $spotProductId)
                              ->whereDate("created_at",$asofdate )->first();
                    
                    if (!empty($freeText)) {
                        $freeText->updated_at = $currentDateTime;
                        $freeText->lastchanged_by = $lastchanged_by;
                        $freeText->save();
                    } else {
                         //Adding freetext comment
                      if(!empty($commentId)) {
                           SupplierProductComments::create([
                            'product_id' => $spotProductId,
                            'pc_id' => NULL,
                            'comment_id' => $commentId,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ]);
                      }
                    }
                    
                    
                    //Adding predefined comment
//                    $preDefined = SupplierProductComments::where('comment_id', $commentRId)->where('product_id', $spotProductId)
//                              ->whereDate("created_at",$asofdate )->first();
//                    
//                     if (!empty($preDefined)) {
//                        $preDefined->updated_at = $currentDateTime;
//                        $preDefined->lastchanged_by = $lastchanged_by;
//                        $preDefined->save();
//                    } else {
//                      if(!empty($commentRId)) {
//                           SupplierProductComments::create([
//                            'product_id' => $spotProductId,
//                            'pc_id' => NULL,
//                            'comment_id' => $commentRId,
//                            'created_at' => $currentDateTime,
//                            'inserted_by' => $insertedby
//                        ]);
//                      }
//                    }
                     

                       //Product Watchlist
                    $exists = DB::table('product_watchlist')->where('product_id', $spotProductId)
                                    ->where('as_of_date', $asofdate)->where('list_type', $watchlist)->where('is_deleted', 0)->first();
                    $watchlistType = !empty($exists->list_type) && isset($exists->list_type) ? Product::getWatchlistType($exists->list_type) : '';
                    $watchlistType = str_replace("Watchlist", "", $watchlistType);

                    if (!empty($exists)) {

                        $msg = '. Product is already available in the ' . $watchlistType . ' watchlist';
                    } else {

                        DB::table('product_watchlist')->insert([
                            'product_id' => $spotProductId,
                            'list_type' => $watchlist,
                            'as_of_date' => $asofdate,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ]);
                        $msg = ' and product is added to watchlist successfully';
                    }

                    $message = 'Comment created successfully' . $msg;
                    $activity = 'Added Comment';
                    $description = 'Added Comment for the product "' . $spotCode . $msg . '" by ' . $username . ' on ' . $formattedDateTime;
                    $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                }


                //Task

                if ($type == 3) {


                    foreach ($userIds as $userId) {
                        $usersTask[] = [
                            'product_id' => $spotProductId,
                            'task_id' => $taskId,
                            'user_id' => $userId,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ];
                    }

                    ProductTaskUser::insert($usersTask);

                    //Sends email notification of assigned task to user/s
                    ProductBackground::sendTaskNotification($userIds, $ac4Code, $spotCode, $comment);
                    $message = 'Task assigned to users successfully';
                    $activity = 'Assigned Task';
                    $description = 'Task assigned to users for the product "' . $spotCode . '" by ' . $username . ' on ' . $formattedDateTime;
                    $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
                }
            }
            return $this->sendResponse($message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Comment/Task creation failed', 'Failed : ' . $error->getMessage(), 208);
        }
    }


    /**
     * Deactivate Comment with product 
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function deactivatecomment(Request $request)
    {
        // $productid = (int) $productid;
        $requestData = $request->all();
        $comment_id = !empty($requestData['comment_id']) && isset($requestData['comment_id']) ? $requestData['comment_id'] : "";

        $currentDateTime = Carbon::now();

        $lastchanged_by = Auth::user()->id;

        //We can delete comment using status flag
        //if it is task for user
        $Comment_status = ProductBackground::where('bg_comment_task_id', $comment_id)->get();

        if ($Comment_status[0]->category == ProductBackground::CATEGORY_TASK && $Comment_status[0]->status == 0) {
            $status = 1;
            try {

                DB::table('bg_comments_tasks')
                        ->where(["bg_comment_task_id" => $comment_id])
                        ->update(['status' => $status, 'lastchanged_by' => $lastchanged_by]);

                return $this->sendResponse('Task Completed', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('Task deactivated failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {
            return $this->sendErrorResponse('Something went wrong please try again later  ', 'Failed : ', 208);
        }
    }

    /**
     * 
     * Get List of all active Tags
     *
     * it return tag_id and name
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function taglist()
    {


        try {
            $taglist = DB::table('tags')
                    ->select('tag_id', 'name')
                    ->where('is_deleted', '=', 0)
                    ->get();

            return $this->sendResponse($taglist, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Tags Data failed to Retrive', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * 
     * Add new Product in product Master Page
     * 
     * @param \illuminate\Http\Request $request
     *
     * @return json Json
     */
    public function addProduct(Request $request)
    {


        $requestData = $request->all();

        $validator = Validator::make($request->all(), [
                    'parent_product_code' => 'required',
                    'product_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }
        $parent_product_code = !empty($requestData['parent_product_code']) && isset($requestData['parent_product_code']) ? $requestData['parent_product_code'] : "";
        $product_code = !empty($requestData['product_code']) && isset($requestData['product_code']) ? $requestData['product_code'] : "";
        $dt_desciption = !empty($requestData['dt_description']) && isset($requestData['dt_description']) ? $requestData['dt_description'] : "";

        $productprice = !empty($requestData['productprice']) && isset($requestData['productprice']) ? $requestData['productprice'] : "";
        $ac1 = !empty($requestData['ac1']) && isset($requestData['ac1']) ? $requestData['ac1'] : "";
        $ac2 = !empty($requestData['ac2']) && isset($requestData['ac2']) ? $requestData['ac2'] : "";
        $currentDateTime = Carbon::now();
        $insertedby = Auth::user()->id;
        $companyId = 207791;
        //dd($companyId);
        try {

            Product::create([
                'ac4' => $parent_product_code,
                'product_code' => $product_code,
                'dt_description' => $dt_desciption,
                'dt_price' => $productprice,
                'ac1' => $ac1,
                'ac2' => $ac2,
                "onboarding_as" => 'Manual', # Product created using product portal Add Product form
                "prod_status" => Product::INCOMPLETE,
                "company_id" => $companyId, //Sigma PLC
                'created_at' => $currentDateTime,
                'inserted_by' => $insertedby,
            ]);

            return $this->sendResponse('Product added successfully', 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to add product', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * 
     * Edit Product in product Master Page
     * 
     * @param \illuminate\Http\Request $request
     *
     * @param $prodid
     * 
     * @return json Json
     */
    public function editProduct(Request $request, $prodid)
    {

        $product = Product::find($prodid);

        if (is_null($product)) {
            return $this->sendErrorResponse('Product not found', 204);
        }


        //Server side validations

        $currentDateTime = Carbon::now();

        try {

            $product->lastchanged_by = Auth::user()->id;
            $product->updated_at = $currentDateTime;
            $product->update($request->all());

            return $this->sendResponse('Product updated successfully', 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update product', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * 
     * View Product in product Master Page
     * 
     * @param \illuminate\Http\Request $request
     *
     * @param $prod_id
     * 
     * @return json Json
     */
    public function viewProduct($prod_id)
    {

        $product = Product::find($prod_id);

        if (is_null($product)) {
            return $this->sendErrorResponse('Product not found', 204);
        }

        try {
            $product_details = DB::select('products')
                    ->where('prod_id', '=', $prod_id)
                    ->select('ac4 as parent_product_code', 'product_code', 'clean_description as dt_description', 'dt_price', 'ac1', 'ac2')
                    ->get();

            return $this->sendResponse($product_details, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed To Get Product Details', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * 
     * Get product codes from parent code (ac4) 
     * 
     * @param \illuminate\Http\Request $request
     *
     * @param string $parentcode AC4 code
     * 
     * @return json Json
     */
    public function productcodes($parentcode)
    {

        try {

            $productcodes = DB::table('products')->where('ac4', '=', $parentcode)->select('product_code', 'prod_id')->where('status', '=', 'Live')->where('is_parent', '=', '0')->get();

            if (is_null($productcodes) || empty($productcodes)) {
                return $this->sendErrorResponse('No any product found', 204);
            }

            return $this->sendResponse($productcodes, 'Successfully retrieve products');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed To Get Products', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * 
     * Get Supplier list with code and name 
     * 
     * @param \illuminate\Http\Request $request
     *
     * @param $parentcode
     * 
     * @return json Json
     */
    public function supplierDetails()
    {

        try {
            $supplierdetails = DB::table('suppliers')->select('id', DB::raw("CONCAT(code, ' - ', name)AS supplier"))->get();
            if (is_null($supplierdetails)) {
                return $this->sendErrorResponse('Suppliers not found', 204);
            }

            return $this->sendResponse($supplierdetails, 'Successfully retrieve data');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed To Get Suppliers List', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * 
     * Gets live AC4
     * 
     * 
     * @return json Json Array
     */
    public function geACProducts()
    {
        $ppCodes = [];
        $ac4 = Product::select('ac4 as parent_product_code', 'clean_description as desc', 'product_desc')->where('ac4', 'not like', '%***%')->where('is_parent', '=', 1)->where('status', '=', 'Live')->orderBy('ac4', 'ASC')->get()->map(function ($ac4) {
            if (empty($ac4->desc)) {
                $ac4->desc = $ac4->product_desc;
            };

            return $ac4;
        });
        foreach ($ac4 as $ac) {
            $ppCodes[$ac["parent_product_code"]] = $ac;
        }

        $data['ac4'] = array_values($ppCodes);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Data retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product data found']);
        }
    }

    /**
     * 
     * Gets Supplier list
     * 
     * @return json Json Array
     */
    public function getSuppliers()
    {

        $data['supplier'] = DB::table('suppliers')->select('id', DB::raw("CONCAT(code, ' - ', name)AS supplier"))->where('type', '=', 1)->get();
        if (!empty($data)) {
            return $this->sendResponse($data, 'Data retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier data found']);
        }
    }

    /**
     * 
     * Gets Supplier list
     * 
     * @return json Json Array
     */
    public function getSupplierCodes()
    {

        $data['supplier'] = DB::table('suppliers')->distinct()->select('id', "code AS supplier")->where('type', '=', 1)->where('company_id', '=', 207791)->get();
        if (!empty($data)) {
            return $this->sendResponse($data, 'Data retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier data found']);
        }
    }

    /**
     * Gets the product contract details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page inventory data
     */
    public function productContractDetails($pr_id)
    {
        $pr_id = (int) $pr_id;
        $data = [];

        $data['contracts'] = Product::getContractDetails($pr_id);

        if (!empty($data['contracts'])) {
            return $this->sendResponse($data, 'Product contract details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product contract details found.']);
        }
    }

    /**
     * Gets the product kpi details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page inventory data
     */
    public function productKpiDetails($pr_id)
    {
        $pr_id = (int) $pr_id;
        $data = [];

        $data['kpi'] = Product::getKpiDetails($pr_id);

        if (!empty($data['kpi'])) {
            return $this->sendResponse($data, 'Product kpi details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product kpi details found.']);
        }
    }

    /**
     * Changes the product kpi details
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page inventory data
     */
    public function changeKpiDetails(Request $request, $pr_id)
    {
        $requestData = $request->all();
        $target_percentage = !empty($requestData['target_percentage']) && isset($requestData['target_percentage']) ? trim($requestData['target_percentage']) : "";
        //$target_percentage = toString($target_percentage);
        $target_percentage = $target_percentage . '%';
        $target_volume = !empty($requestData['target_volume']) && isset($requestData['target_volume']) ? trim($requestData['target_volume']) : "";
        $updated_by = Auth::user()->id;
        $currentDateTime = Carbon::now();

        $pr_id = (int) $pr_id;

        $ac4 = Product::where(["prod_id" => $pr_id])->pluck("products.ac4")->first();

        if (!empty($ac4)) {
            try {
                DB::table('kpi')
                        ->where(['sm_analysis_code2' => $ac4])
                        ->update(['target_percentage' => $target_percentage, 'target_volume' => $target_volume, 'updated_datetime' => $currentDateTime, 'updated_by' => $updated_by]);

                return $this->sendResponse('Kpi Updated Successfully', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('Kpi updation failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product kpi details found.']);
        }
    }

    /**
     * Add Price Indicator/Trend for product Product
     * Only admin can do this activity

     * @param  \App\Product  $productid The Product ID
     *
     * @request data with price_indicator
     * 
     * @return json The Json array to send response
     */
    public function addPriceIndicator(Request $request, $productid)
    {
        $productid = (int) $productid;

        $requestData = $request->all();

        //Server side validations
        $validator = Validator::make($request->all(), [
                    'price_indicator' => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }


        $priceIndicator = !empty($requestData['price_indicator']) && isset($requestData['price_indicator']) ? $requestData['price_indicator'] : "";

        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();

        //Check wheather active price indicatoe is already mapped to that product or not 
        //$productPIExists = ProductPriceIndicator::where('product_id', $productid)->WhereNull('end_date')->first();
        //Deactivate the existing product price indicator
        DB::table('product_price_indicator')
                ->where('product_id', $productid)->WhereNull('end_date')
                ->update(['end_date' => $currentDateTime, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

        //Create new mapping for product price indicator
        try {
            DB::table('product_price_indicator')->insert([
                'product_id' => $productid,
                'price_indicator' => $priceIndicator,
                'created_at' => $currentDateTime,
                'inserted_by' => $insertedby
            ]);

            return $this->sendResponse('Price indicator added to product successfully', 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to add price indicator to product ', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * When we go back to supplier page from supplier mode product page, the rows inside "Products" section should be highlighted with light grey background to indicate that I am alredy visited this particular product.
     *
     * @request data with user id and pricing id
     * 
     * @return json The Json array to send response
     */
    public function storeVisitedPricingItemMapping(Request $request)
    {


        //Check wheather active price indicatoe is already mapped to that product or not 
        //$productPIExists = ProductPriceIndicator::where('product_id', $productid)->WhereNull('end_date')->first();
        //Deactivate the existing product price indicator
        //Create new mapping for product price indicator
        try {
            $requestData = $request->all();
            $userId = Auth::user()->id;
            $pricingId = !empty($requestData['pricing_id']) && isset($requestData['pricing_id']) ? $requestData['pricing_id'] : "";

            $currentDateTime = Carbon::now();

            if (!empty($userId) && !empty($pricingId)) {
                DB::table('user_visited_products')
                        ->where('user_id', $userId)->where('pricing_id', $pricingId)->WhereNull('expired_at')
                        ->update(['expired_at' => $currentDateTime, 'updated_at' => $currentDateTime, 'lastchanged_by' => $userId]);
                DB::table('user_visited_products')->insert([
                    'user_id' => $userId,
                    'pricing_id' => $pricingId,
                    'created_at' => $currentDateTime
                ]);
            } else {
                return $this->sendErrorResponse('Failed to store visited product mapping ', 'Pricing and user reference should be present', 208);
            }

            return $this->sendResponse('Visited product mapping stored successfully', 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to store visited product mapping ', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * Gets the ARI indicator for the product
     * ARI indicator which identifies the supplier that is preferable for that product out of all the suppliers for that product
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of ARI indicator data
     */
    public function getARIIndicator($pr_id)
    {
        $pr_id = (int) $pr_id;
        $data = [];
        $product = DB::table('products')->where(["prod_id" => $pr_id, "status" => "Live"])->count();

        if (empty($product)) {
            return $this->sendErrorResponse('Failed to fetch ARI', 'Product does not exist', 208);
        }
        $data['ari'] = Product::getARIIndicator($pr_id);

        if (!empty($data['ari'])) {
            return $this->sendResponse($data, 'Product ari indicator details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product ari indicator details found.']);
        }
    }

    /**
     * Gets the ARI indicator for the product
     * ARI indicator which identifies the supplier that is preferable for that product out of all the suppliers for that product
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of ARI indicator data
     */
    public function getARIInfo($pr_id)
    {
        $pr_id = (int) $pr_id;
        $data = [];
        $product = DB::table('products')->where(["prod_id" => $pr_id, "status" => "Live"])->count();

        if (empty($product)) {
            return $this->sendErrorResponse('Failed to fetch ARI', 'Product does not exist', 208);
        }
        $data['ari'] = Product::getARIInfo($pr_id);

        if (!empty($data['ari'])) {
            return $this->sendResponse($data, 'Product ari details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product ari indicator details found.']);
        }
    }

    /**
     * Gets the ARI indicator for the product
     * ARI indicator which identifies the supplier that is preferable for that product out of all the suppliers for that product
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of ARI indicator data
     */
    public function getHistoricalARIInfo($pr_id)
    {
        $pr_id = (int) $pr_id;
        $data = [];
        $product = DB::table('products')->where(["prod_id" => $pr_id, "status" => "Live"])->count();

        if (empty($product)) {
            return $this->sendErrorResponse('Failed to fetch ARI', 'Product does not exist', 208);
        }
        $data['ari'] = Product::getHistARIInfo($pr_id);

        if (!empty($data['ari'])) {
            return $this->sendResponse($data, 'Product historical ari details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product ari indicator details found.']);
        }
    }

    /**
     * Updates the ARI indicator supplier from available supplier list
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of ARI indicator data
     */
    public function updateARIndicator(Request $request, $pr_id)
    {
        $requestData = $request->all();
        $ari_ids = !empty($requestData['ari_ids']) && isset($requestData['ari_ids']) ? $requestData['ari_ids'] : [];
        $userId = Auth::user()->id;
        $currentDateTime = Carbon::now();

        if (!empty($ari_ids && $pr_id)) {
            try {

                $product = Product::where(["prod_id" => $pr_id])->pluck("ac4")->first();

                if (empty($product)) {
                    return $this->sendError('NOT FOUND', ['error' => 'No any product found.']);
                }

                foreach ($ari_ids as $supplierId) {
                    $existingAri = DB::table('ari')->where("product_id", $pr_id)->where("supplier_id", $supplierId)->count();
                    $supplierCode = Supplier::where(["id" => $supplierId])->pluck("code")->first();

                    if ($existingAri > 0) {
                        DB::table('ari')
                                ->where('product_id', $pr_id)->where('supplier_id', $supplierId)
                                ->update(['is_manual' => 1, 'updated_at' => $currentDateTime, 'lastchanged_by' => $userId]);
                    } else {

                        DB::table('ari')->insert([
                            'product_id' => $pr_id,
                            'supplier_id' => $supplierId,
                            'AGG CODE' => $product,
                            'BRAND' => $supplierCode,
                            'ARI' => 'ARI',
                            // 'is_ari_supplier' => 1,
                            'is_manual' => 1,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $userId
                        ]);
                    }
                }
                $data['ari'] = Product::getARIIndicator($pr_id);
                return $this->sendResponse('ARI Supplier added successfully', $data, 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('ARI Supplier updation failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any ARI details found.']);
        }
    }

    /**
     * 
     * Gets Supplier list
     * 
     * @return json Json Array
     */
    public function getSuppliersForAri($prodid)
    {
        $allAri = DB::table('ari_indicator')
                        ->where("product_id", $prodid)->where("is_ari_supplier", 1)
                        ->pluck("supplier_id")->toArray();

        $data['supplier'] = DB::table('suppliers')
                        ->select('id', DB::raw("CONCAT(code, ' - ', name)AS supplier"))->where('type', '=', 1)->whereNotIn('id', $allAri)->get();

        if (!empty($data)) {
            return $this->sendResponse($data, 'Data retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any supplier data found']);
        }
    }

    /**
     * Remove PO Item from the selected PO
     *
     * @param int $id Purchase Order Item Id|Primary Key column
     * 
     * @return void
     */
    public function unassignAri($productId, Request $request)
    {

        try {
            $userId = Auth::user()->id;
            $currentDateTime = Carbon::now();
            $requestData = $request->all();
            $ari_id = !empty($requestData['ari_id']) && isset($requestData['ari_id']) ? $requestData['ari_id'] : [];
            $existingAri = DB::table('ari_indicator')->where("ari_id", $ari_id)->where("product_id", $productId)->where("is_ari_supplier", 1)->count();

            if (empty($existingAri)) {
                return $this->sendErrorResponse('Failed to unassign ARI', 'ARI is not assigned to this product', 208);
            }

            DB::table('ari_indicator')->where("ari_id", $ari_id)->where("product_id", $productId)->where("is_ari_supplier", 1)
                    ->update(['is_ari_supplier' => 0, 'updated_at' => $currentDateTime, 'lastchanged_by' => $userId]);
            $data['ari'] = Product::getARIIndicator($productId);
            return $this->sendResponse("ARI is unassigned for this product", $data, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to unassign AR', 'Failed : ' . $error->getMessage(), 209);
        }
    }

    public function getSpotPricing($page, $sortcolumn, $sort)
    {

        $fday = date('Y-m-01');
//        $fday = '2024-01-01';
        $today = date("Y-m-d");
        $pdata = [];
        $sortcolumn;
        $limit = 20;
        $sortcolumn = trim($sortcolumn);
        if ($sortcolumn == 'ProductAC4') {
            $sortcolumn = 'Product_AC_4';
        }
        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $pricingdata = PricingSupplierPriceData::select("pricing_data.parent_product_code")
                        ->where(["pricing_data.source_id" => 10])
                        ->where('price_from_date', '>=', $fday)->where('price_from_date', '<=', $today)
                        ->get()->unique('parent_product_code')->toArray();
        $pricingdata = array_column($pricingdata, "parent_product_code");

        $products = DB::table('DwProduct as p')
                ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                })
//                  ->leftjoin("group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
              //  ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->select('pp.prod_id as productpage_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                        DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                        DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                        'i.Average_usage as Avg Vol',
                        DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                        DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                        DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                        DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                        DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                        DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
//                        DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                        DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                        DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                        DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
                        DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
                        DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
                        DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
                        DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                        'phoenix_outofstock',
                        'trident_outofstock',
                        'cp.aah_outofstock',
                        'colorama_outofstock',
                        'bestway_outofstock',
                        'g.asofdate'
                )
                ->whereIn('p.Product_AC_5', ['SPOT'])
                ->whereIn('p.Product_AC_4', $pricingdata)
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $rowCnt = DB::table('DwProduct as p')
                ->leftjoin("products as pp", function ($join) {
                    $join->on("pp.ac4", "=", "p.Product_AC_4")
                    ->on("pp.is_parent", "=", DB::raw(1));
                })
//                  ->leftjoin("group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
              //  ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->whereIn('p.Product_AC_5', ['SPOT'])
                ->whereIn('p.Product_AC_4', $pricingdata)
                ->count();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product pricing data found']);
        }
    }

    public function searchSpotPricing(Request $request, $page, $sortcolumn, $sort)
    {

        $fday = date('Y-m-01');
//        $fday = '2024-01-01';
        $pdata = [];
        $sortcolumn;
        $limit = 20;
        $sortcolumn = trim($sortcolumn);
        $requestData = $request->all();
        $pcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";

        $today = date("Y-m-d");
        if (!empty($pcode)) {
            if (empty($sortcolumn)) {
                $sortcolumn = 'Product_AC_4';
                $sorder = "ASC";
            } else {
                if ($sort == 1) {
                    $sorder = "ASC";
                } else {
                    $sorder = "DESC";
                }
            }
            $pricingdata = PricingSupplierPriceData::select("pricing_data.parent_product_code")
                            ->where(["pricing_data.source_id" => 10])
                            ->where('price_from_date', '>=', $fday)->where('price_from_date', '<=', $today)
                            ->get()->unique('parent_product_code')->toArray();
            $pricingdata = array_column($pricingdata, "parent_product_code");

            $products = DB::table('DwProduct as p')
                    ->leftjoin("products as pp", function ($join) {
                        $join->on("pp.ac4", "=", "p.Product_AC_4")
                        ->on("pp.is_parent", "=", DB::raw(1));
                    })
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
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->select('pp.prod_id as productpage_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                            DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                            DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                            DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                            'i.Average_usage as Avg Vol',
                            DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                            DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                            DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                            DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                            DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                            DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                            DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                            DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                            DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
                            DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
                            DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
                            DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
                            DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                            'phoenix_outofstock',
                            'trident_outofstock',
                            'aah_outofstock',
                            'colorama_outofstock',
                            'bestway_outofstock',
                            'cp.AsOfDate'
                    )
                    ->whereIn('p.Product_AC_5', ['SPOT'])
                    ->whereIn('p.Product_AC_4', $pricingdata)
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.Product_AC_4', 'like', '%' . $pcode . '%')->orWhere('p.Product_Code', 'like', '%' . $pcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $pcode . '%');
                    })
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $rowCnt = DB::table('DwProduct as p')
                    ->leftjoin("products as pp", function ($join) {
                        $join->on("pp.ac4", "=", "p.Product_AC_4")
                        ->on("pp.is_parent", "=", DB::raw(1));
                    })
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
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->whereIn('p.Product_AC_5', ['SPOT'])
                    ->whereIn('p.Product_AC_4', $pricingdata)
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.Product_AC_4', 'like', '%' . $pcode . '%')->orWhere('p.Product_Code', 'like', '%' . $pcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $pcode . '%');
                    })
                    ->count();

            $pdata["products"] = $products;
            $pdata["rowCnt"] = $rowCnt;
            if (count($products) > 0) {
                return $this->sendResponse($pdata, 'Product pricing retrieved successfully.');
            } else {
                return $this->sendError('NOT FOUND', ['error' => 'No any product pricing data found']);
            }
        } else {
            return $this->sendResponse([], 'Please enter keyword to search product');
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getSupplierPricing($ac4)
    {

        $pdata = [];
        $fday = date('Y-m-01');
//        $fday = '2024-01-01';
        $today = date("Y-m-d");
        $pricingdata = PricingSupplierPriceData::leftjoin('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                        ->select("pricing_data.id as pcid", "suppliers.code as supp_code", "product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "comments", "price_from_date as date")
                        ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
                        ->where('price_from_date', '>=', $fday)->where('price_from_date', '<=', $today)
                        ->orderBy("price_from_date", "DESC")->get()->map(function ($pricingdata) {
            $comments = Comment::join('supplier_product_comments as spc', 'spc.comment_id', '=', 'comments.comment_id')->where('spc.pc_id', $pricingdata->pcid)
                            ->select('comments.title')->get()->toArray();
            $comments = array_column($comments, 'title');
            $commentStr = implode(", ", $comments);
            $pricingdata->buyer_comments = $commentStr;

            return $pricingdata;
        });
//        
//         $ppdata = PricingSupplierPriceData::select("supplier_id", DB::raw("count(supplier_id) as count"))
//                 ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
//                        ->where('price_from_date', '>=', $fday)->where('price_from_date', '<=', $today)
//                    ->groupBy('supplier_id')
//                    ->first()->toArray();
//         $ppsdata = PricingSupplierPriceData::select("supplier_id", "price_from_date", DB::raw("count(supplier_id) as count"))
//                 ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
//                        ->where('price_from_date', '>=', $fday)->where('price_from_date', '<=', $today)
//                    ->groupBy('supplier_id','price_from_date')
//                    ->get()->toArray();
//         dd($ppsdata);
//$pdata = [];
//                foreach ($pricingdata as $data) {
//                    $pdata[$data['supp_code']][] = $data;
//                }
//                dd($pricingdata[0]);
        if (count($pricingdata) > 0) {
            return $this->sendResponse($pricingdata, 'Supplier pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product Supplier data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getHistoricalSupplierPricing($ac4, $page, $sortcolumn, $sort)
    {
        $limit = 20;
        $pdata = [];
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

        $pricingdata = PricingSupplierPriceData::leftjoin('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                        ->select("pricing_data.id as pcid", "suppliers.code as supp_code", "pricing_data.product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "pricing_data.comments", "price_from_date as date")
                        ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($pricingdata) {
            $comments = Comment::join('supplier_product_comments as spc', 'spc.comment_id', '=', 'comments.comment_id')
                            ->where('spc.pc_id', $pricingdata->pcid)
                            ->select('comments.title')->get()->toArray();
            $comments = array_column($comments, 'title');
            $commentStr = implode(", ", $comments);
            $pricingdata->buyer_comments = $commentStr;

            return $pricingdata;
        });
        $rowCnt = PricingSupplierPriceData::leftjoin('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
                ->count();

        $pdata["products"] = $pricingdata;
        $pdata["rowCnt"] = $rowCnt;
        if (count($pricingdata) > 0) {
            return $this->sendResponse($pdata, 'Supplier pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any pricing data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function searchHistoricalSupplierPricing(Request $request, $ac4, $page, $sortcolumn, $sort)
    {
        $limit = 20;
        $pdata = [];
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
        $requestData = $request->all();
        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";

        $pricingdata = PricingSupplierPriceData::leftjoin('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                        ->select("pricing_data.id as pcid", "suppliers.code as supp_code", "pricing_data.product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "pricing_data.comments", "price_from_date as date")
                        ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
                        ->where('price_from_date', '>=', $sdate)->where('price_from_date', '<=', $edate)
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($pricingdata) {
            $comments = Comment::join('supplier_product_comments as spc', 'spc.comment_id', '=', 'comments.comment_id')
                            ->where('spc.pc_id', $pricingdata->pcid)
                            ->select('comments.title')->get()->toArray();
            $comments = array_column($comments, 'title');
            $commentStr = implode(", ", $comments);
            $pricingdata->buyer_comments = $commentStr;

            return $pricingdata;
        });

        $rowCnt = PricingSupplierPriceData::leftjoin('suppliers', 'suppliers.id', '=', 'pricing_data.supplier_id')
                ->select("pricing_data.id as pcid", "suppliers.code as supp_code", "pricing_data.product_code", DB::raw('CAST(price AS DECIMAL(10,2)) AS price'), "forecast", "pricing_data.comments", "price_from_date as date")
                ->where(["pricing_data.parent_product_code" => $ac4, "pricing_data.source_id" => 10])
                ->where('price_from_date', '>=', $sdate)->where('price_from_date', '<=', $edate)
                ->count();

        $pdata["products"] = $pricingdata;
        $pdata["rowCnt"] = $rowCnt;
        if (count($pricingdata) > 0) {
            return $this->sendResponse($pdata, 'Supplier pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any pricing data found']);
        }
    }

    /**
     * Updates the supplier's price by Buyer
     *
     * @param \Illuminate\Http\Request $request Request with Aggregate Code, Product Code, Source ID,
     * Supplier Id and negotiated price
     *
     * @return json The Json response of result
     */
    public function updateSupplierPricing(Request $request)
    {
        $requestData = $request->all();
        //Gets request data

        $negotiated_price = !empty($requestData['negotiated_price']) && isset($requestData['negotiated_price']) ? trim($requestData['negotiated_price']) : "";
        $pricing_id = !empty($requestData['pricing_id']) && isset($requestData['pricing_id']) ? trim($requestData['pricing_id']) : "";
        $scomment = $nforecast = '';
       
        if(isset($requestData['scomment'])) {
            $scomment = trim($requestData['scomment']);
        }
      
        if(isset($requestData['forecast'])) {
            $nforecast = trim($requestData['forecast']);
        }
        
        $currentDateTime = Carbon::now();
        //Today
        $currentDate = date('Y-m-d');

        //Yesterday
        $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
        $inserted_by = $lastchanged_by = Auth::user()->id;
        $pricing = PricingSupplierPriceData::select("source_id", "supplier_id", "product_code", "parent_product_code", "forecast", "comments", "price_from_date", "price_untill_date")->where("id", $pricing_id)->first();

        $ac4 = !empty($pricing['parent_product_code']) && isset($pricing['parent_product_code']) ? trim($pricing['parent_product_code']) : "";

        $product_code = !empty($pricing['product_code']) && isset($pricing['product_code']) ? trim($pricing['product_code']) : "";
        $source_id = !empty($pricing['source_id']) && isset($pricing['source_id']) ? (int) trim($pricing['source_id']) : "";
        $supplier_id = !empty($pricing['supplier_id']) && isset($pricing['supplier_id']) ? (int) trim($pricing['supplier_id']) : "";
        $price_from_date = !empty($pricing['price_from_date']) && isset($pricing['price_from_date']) ? trim($pricing['price_from_date']) : "";
        $price_to_date = !empty($pricing['price_untill_date']) && isset($pricing['price_untill_date']) ? trim($pricing['price_untill_date']) : "";
        $comments = !empty($pricing['comments']) && isset($pricing['comments']) ? trim($pricing['comments']) : "";
        $forecast =  trim($pricing['forecast']);
        $username = Auth::user()->getName();
     
        $formattedDateTime = Carbon::parse($currentDateTime)->format('d-M-Y h:i A');
        try {
             
               
            
           if ($request->has('scomment')) {
               
                DB::table('pricing_data')
                        ->where(["id" => $pricing_id])
                        ->update(['comments' => $scomment, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                $msg = "Supllier comment updated successfully";
                $headMsg = 'Pricing line updated';
                
                $activity = 'Supllier Comments Updated';
                $description = 'Supllier comments updated for the product "' . $product_code . '" by ' . $username . ' on ' . $formattedDateTime;
                $this->activityLogService->logActivity($inserted_by, $activity, $description, $request);
            }

            if ($request->has('forecast')) {
                DB::table('pricing_data')
                        ->where(["id" => $pricing_id])
                        ->update(['forecast' => $nforecast, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                $msg = "Forecast value updated successfully";
                $headMsg = 'Pricing line updated';
                
                $activity = 'Forecast Updated';
                $description = 'Forecast value updated as "' . $nforecast . '" for the product "' . $product_code . '" by ' . $username . ' on ' . $formattedDateTime;
                $this->activityLogService->logActivity($inserted_by, $activity, $description, $request);
            }

            if (!empty($negotiated_price)) {
                //Creates new pricing record with new date range

                if ($price_from_date > $yesterday) {
                    $todate = $currentDate;
                } else {
                    $todate = $yesterday;
                }


                //Updates the price_untill_date of old/exiting/historical record
                DB::table('pricing_data')
                        ->where(["id" => $pricing_id])
                        ->update(['negotiated_price' => $negotiated_price, 'price_untill_date' => $todate, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

                if ($price_to_date < $currentDate) {
                    $price_to_date = $currentDate;
                }

                PricingSupplierPriceData::create([
                    'source_id' => $source_id,
                    'supplier_id' => $supplier_id,
                    'parent_product_code' => $ac4, // Parent Product Code
                    'product_code' => $product_code, //Child Product Code
                    'price' => floatval($negotiated_price),
                    'forecast' => $forecast,
                    'comments' => $comments,
                    'price_from_date' => $currentDate,
                    'price_untill_date' => $price_to_date,
                    'import_type' => PricingSupplierPriceData::PRICE_TYPE_NEGOTIATED_PRICE,
                    'price_type' => PricingSupplierPriceData::PRICE_TYPE_NEGOTIATED_PRICE,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                    'inserted_by' => $inserted_by,
                    'lastchanged_by' => $lastchanged_by
                ]);

                $msg = "Price updated successfully.";
                $headMsg = 'Negotiated Price Updated';
                $activity = 'Price Updated';
                $description = 'Price updated as "' . floatval($negotiated_price) . '" for the product "' . $product_code . '" by ' . $username . ' on ' . $formattedDateTime;
                $this->activityLogService->logActivity($inserted_by, $activity, $description, $request);
            }
            return $this->sendResponse($headMsg, $msg);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update price', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * Attach Tag With Product
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function addProductComment(Request $request)
    {


        $requestData = $request->all();

        $product_id = !empty($requestData['product_id']) && isset($requestData['product_id']) ? $requestData['product_id'] : "";
        $pcid = !empty($requestData['pcid']) && isset($requestData['pcid']) ? $requestData['pcid'] : "";
        $custom_comment = !empty($requestData['custom_comment']) && isset($requestData['custom_comment']) ? $requestData['custom_comment'] : "";
        $comment_id = !empty($requestData['comment_id']) && isset($requestData['comment_id']) ? $requestData['comment_id'] : "";
        $username = Auth::user()->getName();
        $currentDateTime = Carbon::now();
        $formattedDateTime = Carbon::parse($currentDateTime)->format('d-M-Y h:i A');
        $spotCode = DB::table('dbo.DwProduct')->where("Product_Id", $product_id)->pluck("Product_Code")->first();
        $list_type = 1;
        $errorStr = '';
        //Server side validations
        $validator = Validator::make($request->all(), [
                    'pcid' => "required"
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }


        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();

        if (!empty($custom_comment) && empty($comment_id)) {
            $comment = Comment::where('title', $custom_comment)->where('is_deleted', 0)->select('comment_id')->first();
            if (empty($comment)) {
                $comment = Comment::create([
                            'title' => $custom_comment,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby,
                            'cgroup' => 1
                ]);
                
                $activity = 'Added Comments';
        $description = 'Added comments for the product "' . $spotCode . '" by ' . $username . ' on ' . $formattedDateTime;
        $this->activityLogService->logActivity($insertedby, $activity, $description, $request);
            }

            $comment_id = $comment->comment_id;
        }


        //if ($severity == null) {  // bydefault severity low
        //}
        //Check wheather active tag is already mapped to that product or not 

        $productTagExists = SupplierProductComments::where('comment_id', $comment_id)->where('pc_id', $pcid)->first();

        if (!empty($productTagExists)) {

            try {
                //Updates the existing product tag mapping
                $productTagExists->updated_at = $currentDateTime;
                $productTagExists->lastchanged_by = $lastchanged_by;
                $productTagExists->save();

                DB::table('comments')
                        ->where(['comment_id' => $comment_id, 'cgroup' => 1])
                        ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

                // return $this->sendResponse('Comment updated successfully', 200);
            } catch (\Exception $error) {
                $errorStr .= 'Adding comment failed : ' . $error->getMessage();
//                return $this->sendErrorResponse('Adding comment failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {

            try {

                SupplierProductComments::create([
                    'product_id' => $product_id,
                    'pc_id' => $pcid,
                    'comment_id' => $comment_id,
                    'created_at' => $currentDateTime,
                    'inserted_by' => $insertedby
                ]);

                // return $this->sendResponse('Comment added to product successfully', 200);
            } catch (\Exception $error) {
                $errorStr .= 'Adding comment failed : ' . $error->getMessage();
//                return $this->sendErrorResponse('Adding comment to product failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        }
        
        
        return $this->addProductToWatchlist($product_id, $currentDateTime, $list_type, $errorStr);
        
        
    }

    /**
     * Attach Tag With Product
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function addPricierComment(Request $request)
    {


        $requestData = $request->all();

        $product_id = !empty($requestData['product_id']) && isset($requestData['product_id']) ? $requestData['product_id'] : "";
        $custom_comment = !empty($requestData['custom_comment']) && isset($requestData['custom_comment']) ? $requestData['custom_comment'] : "";
        $comment_id = !empty($requestData['comment_id']) && isset($requestData['comment_id']) ? $requestData['comment_id'] : "";

        //Server side validations
        $validator = Validator::make($request->all(), [
                    'product_id' => "required"
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }


        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();

        if (!empty($custom_comment) && empty($comment_id)) {
            $comment = Comment::where('title', $custom_comment)->where('is_deleted', 0)->select('comment_id')->first();
            if (empty($comment)) {
                $comment = Comment::create([
                            'title' => $custom_comment,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby,
                            'cgroup' => 2
                ]);
            }

            $comment_id = $comment->comment_id;
        }


        //if ($severity == null) {  // bydefault severity low
        //}
        //Check wheather active tag is already mapped to that product or not 

        $pricierCommentExists = PricierComments::where('comment_id', $comment_id)->where('product_id', $product_id)->first();

        if (!empty($pricierCommentExists)) {

            try {
                //Updates the existing product tag mapping
                $pricierCommentExists->updated_at = $currentDateTime;
                $pricierCommentExists->lastchanged_by = $lastchanged_by;
                $pricierCommentExists->save();

                DB::table('comments')
                        ->where(['comment_id' => $comment_id, 'cgroup' => 2])
                        ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

                return $this->sendResponse('Comment updated successfully', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('Adding comment failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {

            try {

                PricierComments::create([
                    'product_id' => $product_id,
                    'comment_id' => $comment_id,
                    'created_at' => $currentDateTime,
                    'inserted_by' => $insertedby
                ]);

                return $this->sendResponse('Comment added to product successfully', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('Adding comment to product failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        }
    }

    /**
     * Deactivate tag with product 
     *
     * @param  \App\Models\PricingSupplierPriceData  $pc_id The Pricing data ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function removeTag(Request $request, $pc_id)
    {

        $requestData = $request->all();

        $tag_id = !empty($requestData['tag_id']) && isset($requestData['tag_id']) ? $requestData['tag_id'] : "";
        $severity = !empty($requestData['severity']) && isset($requestData['severity']) ? $requestData['severity'] : "";
        $currentDateTime = Carbon::now();

        $lastchanged_by = Auth::user()->id;
        //We can delete tag using date we can add date in end_date after adding end_date tag is going to historical state

        try {

            DB::table('supplier_product_tags')
                    ->where(["pc_id" => $pc_id, "tag_id" => $tag_id, "severity" => $severity])
                    ->update(['end_date' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

            return $this->sendResponse('Tag is unlinked successfully ', 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Tag unlinking failed ', 'Failed : ' . $error->getMessage(), 208);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getCompetitorContractPricing($spot, $page, $sortcolumn, $sort)
    {

        $pdata = [];
        $limit = 20;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $products = DB::table('competitor_prices as cp')
                ->leftjoin("DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                //->leftjoin("group_pricing as g", function ($join) {
//                        $join->on("g.product_code", "=", "p.Product_Code")
//                        ->on("g.AsOfDate", "=", "cp.AsOfDate");
//                    })
                ->select('g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                        DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                        DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                        'i.Average_usage as Avg Vol',
                        DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                        DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                        DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                        DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                        DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                        DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                        DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                        DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                        DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
                        DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
                        DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
                        DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
                        DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                        'cp.phoenix_outofstock',
                        'cp.trident_outofstock',
                        'cp.aah_outofstock',
                        'cp.colorama_outofstock',
                        'cp.bestway_outofstock',
                        'cp.AsOfDate as date'
                )
                ->where('p.Product_Code', '=', $spot)
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $rowCnt = DB::table('competitor_prices as cp')
                ->leftjoin("DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                //->leftjoin("group_pricing as g", function ($join) {
//                        $join->on("g.product_code", "=", "p.Product_Code")
//                        ->on("g.AsOfDate", "=", "cp.AsOfDate");
//                    })
                ->where('p.Product_Code', '=', $spot)
                ->count();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product pricing data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function searchCompetitorContractPricing(Request $request, $spot, $page, $sortcolumn, $sort)
    {

        $pdata = [];
        $limit = 20;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
        $requestData = $request->all();
        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";

        $products = DB::table('competitor_prices as cp')
                ->leftjoin("DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                //->leftjoin("group_pricing as g", function ($join) {
//                        $join->on("g.product_code", "=", "p.Product_Code")
//                        ->on("g.AsOfDate", "=", "cp.AsOfDate");
//                    })
                ->select('g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                        DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                        DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                        'i.Average_usage as Avg Vol',
                        DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                        DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                        DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                        DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                        DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                        DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                        DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                        DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                        DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
                        DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
                        DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
                        DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
                        DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                        'cp.phoenix_outofstock',
                        'cp.trident_outofstock',
                        'cp.aah_outofstock',
                        'cp.colorama_outofstock',
                        'cp.bestway_outofstock',
                        'cp.AsOfDate as date'
                )
                ->where('p.Product_Code', '=', $spot)
                ->where('cp.AsOfDate', '>=', $sdate)->where('cp.AsOfDate', '<=', $edate)
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $rowCnt = DB::table('competitor_prices as cp')
                ->leftjoin("DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                ->where('p.Product_Code', '=', $spot)
                ->where('cp.AsOfDate', '>=', $sdate)->where('cp.AsOfDate', '<=', $edate)
                ->count();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product pricing data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getCompetitorPricing($page, $sortcolumn, $sort)
    {

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
        $group = 3;
        $prdata = Product::getCompetitorPricingDataRDS($group, $page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Competitor pricing for RDS retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getCompetitorPricingForPreset($page, $sortcolumn, $sort)
    {

//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
        $group = 2;
        $prdata = Product::getCompetitorPricingData($group, $page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Competitor pricing for Preset watchlist retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getCompetitorPricingForPresetTwiceAWeek($page, $sortcolumn, $sort)
    {

//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
        $group = 6;
        $prdata = Product::getCompetitorPricingData($group, $page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Competitor pricing for Preset watchlist for Jayleshbhai retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }

    /**
     * Get Buyer Intel watchlist
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getCompetitorPricingForBuyerSet($page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
        $group = 1;
        $prdata = Product::getCompetitorPricingData($group, $page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Competitor pricing for Buyer defined watchlist retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }
    
    
    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getCompetitorPricingForBuyerWatchlist($page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
        $group = 4;
        $prdata = Product::getCompetitorPricingData($group, $page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Competitor pricing for Buyer defined watchlist retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }

    
     /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getUndecostlinesWatchlist($page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
       
        $prdata = Product::getUndecostlinesWatchlist($page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Undercost lines retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }
    
     /**
     * Get Buyer Intel watchlist
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getUndecostlines($page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
        $group = 7;
        $prdata = Product::getCompetitorPricingData($group, $page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Undercost lines retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any undercost lines data found']);
        }
    }
    
         /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getUndecostlinesOld($page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
//        $today = '2023-12-25';

        $pdata = [];

//        $user = Auth::user();
//        $group = $user->getType();
       
        $prdata = Product::getUndecostlines($page, $sortcolumn, $sort);
        $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
        $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Undercost lines retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any undercost lines data found']);
        }
    }
    
    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function searchCompetitorPricing(Request $request, $page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
        $requestData = $request->all();

        $prodcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";
        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";
        $group = !empty($requestData['group']) && isset($requestData['group']) ? $requestData['group'] : "";
        $pdata = [];
        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if (empty($sortcolumn)) {
            $sortcolumn = 'AsOfDate';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }



        if (!empty($prodcode) && empty($sdate) && empty($edate)) {
            $prdata = Product::searchCompetitorPricingByProduct($group, $prodcode, $page, $sortcolumn, $sorder, $limit);
            $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
            $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        } else if (empty($prodcode) && !empty($sdate) && !empty($edate)) {
            $prdata = Product::searchCompetitorPricingByDate($group, $sdate, $edate, $page, $sortcolumn, $sorder, $limit);
            $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
            $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        } else {
            $prdata = Product::searchCompetitorPricingByProductDate($group, $prodcode, $sdate, $edate, $page, $sortcolumn, $sorder, $limit);
            $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
            $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        }


        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if ($products) {
            return $this->sendResponse($pdata, 'Competitor pricing retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function downloadCompetitorPricing(Request $request)
    {
        $requestData = $request->all();

        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";
        $group = !empty($requestData['group']) && isset($requestData['group']) ? $requestData['group'] : "";
        if (!empty($sdate) && !empty($edate)) {
            $products = Product::downloadCompetitorPricing($group, $sdate, $edate);
        }



        if ($products) {
            return $this->sendResponse($products, 'Competitor pricing downloaded successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor pricing data found']);
        }
    }

    /**
     * Add|Update price review
     *
     *
     * @request ProductAC4 , SPOTCode, asofdate, phoenix, trident, aah, colorama,bestway,phoenix_outofstock,trident_outofstock,colorama_outofstock,aah_outofstock,bestway_outofstock
     * 
     * @return json The Json array to send response
     */
    public function addUpdateCompetitorPricing(Request $request)
    {


        $requestData = $request->all();
        $watchlist_id = !empty($requestData['watchlist_id']) && isset($requestData['watchlist_id']) ? $requestData['watchlist_id'] : "";
        $product_id = !empty($requestData['product_id']) && isset($requestData['product_id']) ? $requestData['product_id'] : "";

        $asofdate = date("Y-m-d");
        $phoenix = !empty($requestData['phoenix']) && isset($requestData['phoenix']) ? $requestData['phoenix'] : NULL;
        $trident = !empty($requestData['trident']) && isset($requestData['trident']) ? $requestData['trident'] : NULL;
        $aah = !empty($requestData['aah']) && isset($requestData['aah']) ? $requestData['aah'] : NULL;
        $colorama = !empty($requestData['colorama']) && isset($requestData['colorama']) ? $requestData['colorama'] : NULL;
        $bestway = !empty($requestData['bestway']) && isset($requestData['bestway']) ? $requestData['bestway'] : NULL;
        $phoenix_outofstock = !empty($requestData['phoenix_outofstock']) && isset($requestData['phoenix_outofstock']) ? $requestData['phoenix_outofstock'] : 0;
        $trident_outofstock = !empty($requestData['trident_outofstock']) && isset($requestData['trident_outofstock']) ? $requestData['trident_outofstock'] : 0;
        $aah_outofstock = !empty($requestData['aah_outofstock']) && isset($requestData['aah_outofstock']) ? $requestData['aah_outofstock'] : 0;
        $colorama_outofstock = !empty($requestData['colorama_outofstock']) && isset($requestData['colorama_outofstock']) ? $requestData['colorama_outofstock'] : 0;
        $bestway_outofstock = !empty($requestData['bestway_outofstock']) && isset($requestData['bestway_outofstock']) ? $requestData['bestway_outofstock'] : 0;
        $group = !empty($requestData['group']) && isset($requestData['group']) ? $requestData['group'] : "";
         //Notes
        $phoenix_note = !empty($requestData['phoenix_note']) && isset($requestData['phoenix_note']) ? $requestData['phoenix_note'] : '';
        $trident_note = !empty($requestData['trident_note']) && isset($requestData['trident_note']) ? $requestData['trident_note'] : '';
        $aah_note = !empty($requestData['aah_note']) && isset($requestData['aah_note']) ? $requestData['aah_note'] :'';
        $colorama_note = !empty($requestData['colorama_note']) && isset($requestData['colorama_note']) ? $requestData['colorama_note'] : '';
        $bestway_note = !empty($requestData['bestway_note']) && isset($requestData['bestway_note']) ? $requestData['bestway_note'] : '';
//        $alliance_note = !empty($requestData['alliance_note']) && isset($requestData['alliance_note']) ? $requestData['alliance_note'] : NULL;
//        $otc_note = !empty($requestData['otc_note']) && isset($requestData['otc_note']) ? $requestData['otc_note'] : NULL
        //Server side validations
        $validator = Validator::make($request->all(), [
                    'product_id' => "required",
//                    'SPOTCode' => "required",
                    'asofdate' => "required",
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }


        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();
        $productid = DB::table('dbo.DwProduct')->select("Product_Id")->where("Product_Id", $product_id)->pluck("Product_Id")->first();

        if (empty($productid)) {
            return $this->sendErrorResponse('Price Review failed', 'Failed : ' . 'Product is not available in the system', 208);
        }

        //Check wheather active tag is already mapped to that product or not 
        $priceReviewExists = CompetitorPricing::where('product_id', $productid)
                ->where('group', $group)
                ->where('AsOfDate', $asofdate)->where('actioned', 0)->first();

        if (!empty($priceReviewExists)) {

            try {
                //Updates the existing price review
                $priceReviewExists->phoenix = $phoenix;
                $priceReviewExists->trident = $trident;
                $priceReviewExists->aah = $aah;
                $priceReviewExists->colorama = $colorama;
                $priceReviewExists->bestway = $bestway;
                $priceReviewExists->phoenix_outofstock = $phoenix_outofstock;
                $priceReviewExists->trident_outofstock = $trident_outofstock;
                $priceReviewExists->aah_outofstock = $aah_outofstock;
                $priceReviewExists->colorama_outofstock = $colorama_outofstock;
                $priceReviewExists->bestway_outofstock = $bestway_outofstock;
                $priceReviewExists->phoenix_note = $phoenix_note;
                $priceReviewExists->trident_note = $trident_note;
                $priceReviewExists->aah_note = $aah_note;
                $priceReviewExists->colorama_note = $colorama_note;
                $priceReviewExists->bestway_note = $bestway_note;
//                $priceReviewExists->AsOfDate = $asofdate;
                $priceReviewExists->updated_at = $currentDateTime;
                $priceReviewExists->lastchanged_by = $lastchanged_by;
                $priceReviewExists->group = $group;
                $priceReviewExists->save();

   

                return $this->sendResponse([], 'Price review updated successfully', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('Price Review failed', 'Failed : ' . $error->getMessage(), 208);
            }
        } else {

            try {

                CompetitorPricing::create([
                    'watchlist_id' => $watchlist_id,
                    'product_id' => $productid,
                    'phoenix' => $phoenix,
                    'trident' => $trident,
                    'aah' => $aah,
                    'colorama' => $colorama,
                    'bestway' => $bestway,
                    'phoenix_outofstock' => $phoenix_outofstock,
                    'trident_outofstock' => $trident_outofstock,
                    'aah_outofstock' => $aah_outofstock,
                    'colorama_outofstock' => $colorama_outofstock,
                    'bestway_outofstock' => $bestway_outofstock,
                     'phoenix_note' => $phoenix_note,
                     'trident_note' => $trident_note,
                     'aah_note' => $aah_note,
                     'colorama_note' => $colorama_note,
                     'bestway_note' => $bestway_note,
                    'group' => $group,
                    'AsOfDate' => $asofdate,
                    'created_at' => $currentDateTime,
                    'inserted_by' => $insertedby
                ]);

                return $this->sendResponse([], 'Price review added successfully', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse('Price Review failed', 'Failed : ' . $error->getMessage(), 208);
            }
        }
    }

    
    
    
    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function downloadPriceReview($type, Request $request)
    {
        $today = date("Y-m-d");
//        $type = !empty($requestData['group']) && isset($requestData['group']) ? $requestData['group'] : "";
        $requestData = $request->all();
        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";
       
        if($type == 3) {
        $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
//                 ->leftjoin("group_pricing as g", "g.product_code", "=", "p.Product_Code")
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
                        ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                         ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
//                                DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                   DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
//                                DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
//                                   DB::raw('CAST(g.true_cost AS DECIMAL(10,2)) AS "True_Cost"'),
                                     DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS True_Cost"),
//                                DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS 'Avg_Cost'"),
//                                'i.Average_usage as Avg Vol',
                                 DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Avg_Vol"),
                    
                               DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
//                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
//                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                //DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                //DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
//                                DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                               // DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
//                                DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.phoenix_note',
                                'cp.trident_note',
                                'cp.aah_note',
                                'cp.colorama_note',
                                'cp.bestway_note',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage'
                        )
                        ->where('pw.list_type', '=', $type)
                        ->where('pw.as_of_date', $today)
                        ->where('pw.status', 1)
                        ->where('cp.AsOfDate', '>=', $sdate)->where('cp.AsOfDate', '<=', $edate)
                        ->orderBy('p.Product_AC_4')
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });
        } elseif ($type == 7) {
         $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                        ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'cp.AsOfDate');
                            })
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                DB::raw("REPLACE(REPLACE(CAST(g.COST AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[TRUE COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG COST] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.[AVG VOL] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
//                              
                                 DB::raw("REPLACE(REPLACE(CAST(g.[87] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
//                                DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                DB::raw('CAST(NULL AS VARCHAR) as new_c87'),
                                DB::raw('CAST(NULL AS VARCHAR)  as new_c122'),
                                DB::raw('CAST(NULL AS VARCHAR)  as new_DC'),
                                DB::raw('CAST(NULL AS VARCHAR) as new_DG'),
                                DB::raw('CAST(NULL AS VARCHAR)  as new_RH'),
                                DB::raw('CAST(NULL AS VARCHAR)  as new_RBS'),
                                 DB::raw('CAST(NULL AS VARCHAR) as new_ATOZ'),
                                 DB::raw('CAST(NULL AS VARCHAR) as new_RRP'),
                            
                               DB::raw("REPLACE(REPLACE(CAST(g.[122] AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
//                                DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
//                                DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                               
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
//                                DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
//                                DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
//                                DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                               // DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
//                                DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
//                                DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                         
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.phoenix_note',
                                'cp.trident_note',
                                'cp.aah_note',
                                'cp.colorama_note',
                                'cp.bestway_note',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage',
                                'g.BrokenRule'
                        )
                        ->where('cp.group', '=', $type)
                          ->where('cp.actioned', 1)  
                            ->where('cp.AsOfDate', '>=', $sdate)->where('cp.AsOfDate', '<=', $edate)
                        ->orderBy('p.Product_AC_4')
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });
    }else {
             $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
//                 ->leftjoin("group_pricing as g", "g.product_code", "=", "p.Product_Code")
                        ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
//                        ->leftjoin('competitor_prices as cp', function ($join) {
//                            $join->on('p.Product_Id', '=', 'cp.product_id')
//                            ->whereRaw(
//                                    'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
//                            );
//                        })
                        ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
//                                DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                   DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
//                                DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
//                                   DB::raw('CAST(g.true_cost AS DECIMAL(10,2)) AS "True_Cost"'),
                                  DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS True_Cost"),
//                                DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS 'Avg_Cost'"),
//                                'i.Average_usage as Avg Vol',
                                   DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Avg_Vol"),
//                                DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                                DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
//                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
//                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                //DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                                DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                //DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
//                                DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                               // DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
//                                DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                         
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.phoenix_note',
                                'cp.trident_note',
                                'cp.aah_note',
                                'cp.colorama_note',
                                'cp.bestway_note',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage'
                        )
                        ->where('cp.group', '=', $type)
                          ->where('cp.actioned', 1)  
                            ->where('cp.AsOfDate', '>=', $sdate)->where('cp.AsOfDate', '<=', $edate)
                        ->orderBy('p.Product_AC_4')
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });
        }

        if (count($products) > 0) {
            return $this->sendResponse($products, 'Product pricing downloaded successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product pricing data found']);
        }
    }

    /**
     * Gets the product details like product parent code. description
     *
     * @param  \App\Models\Product  $productid The Product ID
     *
     * @return json The Json array of Product Page data
     */
    public function getAnalyticsPageHeader($prodcode)
    {

        $data = [];

        $data['header'] = Product::getAnalyticsPageHeader($prodcode);

        if (!empty($data)) {
            return $this->sendResponse($data, 'Analytics page header details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Product header details found']);
        }
    }

    /**
     * API to search comment
     * Search supplier by keyword of comment

     * @param string $term The search Parameter
     *
     * @return json The Json Array of Comments Matching list
     */
    public function searchComment($keyword)
    {
        $keyword = strtolower($keyword);
        $comments = DB::table('comments')
                        ->where(DB::raw("LOWER(title)"), 'LIKE', '%' . $keyword . '%')
                        ->select('comment_id as key', 'title as value')->get(); // Removed Space from right side and left side from column

        return response()->json(["comments" => $comments]);
    }

     /**
     * API to search pre-decided comment

     * @param string $keyword The search Parameter
     *
     * @return json The Json Array of Comments Matching list
     */
    public function searchPredefinedComment($keyword)
    {
        $keyword = strtolower($keyword);
        $comments = DB::table('comments')
                        ->where(DB::raw("LOWER(title)"), 'LIKE', '%' . $keyword . '%')
                        ->where("cgroup", 4)
                        ->select('comment_id as value', 'title as lable')->get(); // Removed Space from right side and left side from column

        return response()->json(["comments" => $comments]);
    }
    
    /**
     * API to get pre-decided comments list
     *
     *
     * @return json The Json Array of list of pre-defined comments
     */
    public function getPredefinedComment()
    {
       
        $comments = DB::table('comments')
                        ->where("cgroup", 4)
                        ->select('comment_id as value', 'title as lable')->get(); // Removed Space from right side and left side from column

        return response()->json(["comments" => $comments]);
    }
    
    /**
     * API to search Competitor
     * Search supplier by keyword of Competitor

     * @param string $term The search Parameter
     *
     * @return json The Json Array of Competitor Matching list
     */
    public function searchCompetitor($keyword)
    {
        $keyword = strtolower($keyword);
        $competitors = DB::table('competitors')
                        ->where(DB::raw("LOWER(name)"), 'LIKE', '%' . $keyword . '%')
                        ->select('competitor_id as key', 'name as value')->get();

        return response()->json(["competitors" => $competitors]);
    }

    private function addProductToWatchlist($product_id, $asofdate, $list_type, $errorStr)
    {
        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();
       

//        $exists = DB::table('product_watchlist')->where('product_id', $product_id)->where('list_type', $list_type)
//                        ->where('as_of_date', $asofdate)->where('is_deleted', 0)->first();

        $exists = DB::table('product_watchlist')->where('product_id', $product_id)
                        ->where('as_of_date', $asofdate)->where('list_type', $list_type)->where('is_deleted', 0)->first();

        $type = !empty($exists->list_type) && isset($exists->list_type) ? Product::getWatchlistType($exists->list_type) : '';

        if (!empty($exists)) {


            return $this->sendResponse('Added comment. Product is already available in the ' . $type . ' watchlist', 200);
        } else {

            try {

                DB::table('product_watchlist')->insert([
                    'product_id' => $product_id,
                    'list_type' => $list_type,
                    'as_of_date' => $asofdate,
                    'created_at' => $currentDateTime,
                    'inserted_by' => $insertedby
                ]);

                return $this->sendResponse('Added comment and product is added to watchlist successfully', 200);
            } catch (\Exception $error) {
                return $this->sendErrorResponse($errorStr . 'Added comment. Adding product to watchlist failed ', 'Failed : ' . $error->getMessage(), 208);
            }
        }
        
        
    }

    /**
     * addToWatchList
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function addToWatchList(Request $request)
    {

        $errorStr = '';
        $requestData = $request->all();

        $product_id = !empty($requestData['product']) && isset($requestData['product']) ? $requestData['product'] : "";
        $list_type = !empty($requestData['type']) && isset($requestData['type']) ? $requestData['type'] : "";
        $asofdate = !empty($requestData['as_of_date']) && isset($requestData['as_of_date']) ? $requestData['as_of_date'] : "";
        //Server side validations
        $validator = Validator::make($request->all(), [
                    'product' => "required",
                    'type' => "required"
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        return $this->addProductToWatchlist($product_id, $asofdate, $list_type, $errorStr);
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $flg = FALSE;
        
        if(!empty($date)) {
        $d = \DateTime::createFromFormat($format, $date);
        
        if ($d->format($format) === $date) {
            $flg = TRUE;
        }
        }
        return $flg;
    }

    /**
     * Import Watchlist
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function importWatchList(Request $request, $list_type)
    {


        $products = $request->all();
       

        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();
        $scount = $dcount = 0;
        $error = [];
        $errorStr = '';
        $incorrectData = 0;
        foreach ($products as $product) {

            $ac4 = !empty($product['AGGCODE']) && isset($product['AGGCODE']) ? $product['AGGCODE'] : "";
            $prodcode = !empty($product['PRODCODE']) && isset($product['PRODCODE']) ? $product['PRODCODE'] : "";
            $asofdate = !empty($product['DATE']) && isset($product['DATE']) ? $product['DATE'] : NULL;
            $day = !empty($product['DAY']) && isset($product['DAY']) ? strtolower($product['DAY']) : "";
            $product_id = DB::table('DwProduct')->where('Product_AC_4', $ac4)->where('Product_Code', $prodcode)
                            ->pluck('Product_Id')->first();
     
         
            if (!empty($product_id) && (!empty($asofdate) || !empty($day)) && !empty($list_type)) {
                
            //"PRESET FOR OFFICE-DAILY"
            if($list_type == 2) {
             
                    $exists = DB::table('product_watchlist')->where('product_id', $product_id)
                                ->where('is_deleted', 0)->where('list_type', $list_type)->first(); 
            } 
            //"PRESET FOR OFFICE - TWICE A WEEK"
            if($list_type == 6) {
             
                    $exists = DB::table('product_watchlist')->where('product_id', $product_id)
                                ->where('is_deleted', 0)->where('list_type', $list_type)->where('day', $day)->first(); 
             }
             //"RDS"
            if(!empty($asofdate) && $this->validateDate($asofdate, 'Y-m-d') !== false && $list_type == 3) {
               $exists = DB::table('product_watchlist')->where('product_id', $product_id)
                        ->where('as_of_date', $asofdate)->where('list_type', $list_type)->where('status', 0)->where('is_deleted', 0)->first(); 
            }
                

                $type = !empty($exists->list_type) && isset($exists->list_type) ? Product::getWatchlistType($exists->list_type) : '';
             
                if (empty($exists)) {
                    try {

                        DB::table('product_watchlist')->insert([
                            'product_id' => $product_id,
                            'list_type' => $list_type,
                            'as_of_date' => $asofdate,
                            'day' => $day,
                            'created_at' => $currentDateTime,
                            'inserted_by' => $insertedby
                        ]);

                        $scount++;
                    } catch (\Exception $er) {
                        $error[] = $er->getMessage();
                    }
                } else {
                    $dcount++;
                }
             } else {
                 $incorrectData++;
             }
        
        }

        if (!empty($error)) {
            $errorStr = implode(", ", $error);
            return $this->sendErrorResponse('Adding products to watchlist failed ', 'Failed : ' . $errorStr, 208);
        }
        $inc = '';
        if (!empty($incorrectData)) {
            $inc = ' Incorrect data found for '. $incorrectData .' line.' ;
        }
        if (!empty($dcount) && !empty($scount)) {
            return $this->sendResponse('Found ' . $dcount . ' duplicate product/s, some products are alredy available in the ' . $type . ' watchlist. ' . $scount . ' Products is/are added to watchlist successfully.'. $inc, 200);
        }
        if (!empty($dcount) && empty($scount)) {
            return $this->sendResponse('These ' . $dcount . ' products are alredy available in the ' . $type . ' watchlist.'. $inc, 200);
        }
        if (empty($dcount) && !empty($scount)) {
            return $this->sendResponse($scount . ' Products are added to watchlist successfully.'. $inc, 200);
        }
    }

    /**
     * addBulkToWatchList
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function addBulkToWatchList(Request $request)
    {


        $requestData = $request->all();

        $products = !empty($requestData['products']) && isset($requestData['products']) ? $requestData['products'] : "";
        $list_type = !empty($requestData['type']) && isset($requestData['type']) ? $requestData['type'] : "";
        $asofdate = !empty($requestData['as_of_date']) && isset($requestData['as_of_date']) ? $requestData['as_of_date'] : "";

        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();
        //Server side validations
        $validator = Validator::make($request->all(), [
                    'products' => "required",
                    'type' => "required"
        ]);
        $scount = $dcount = 0;
        $error = [];
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        foreach ($products as $product) {
            $product_id = !empty($product['product_id']) && isset($product['product_id']) ? $product['product_id'] : "";

//            $exists = DB::table('product_watchlist')->where('product_id', $product_id)->where('list_type', $list_type)
//                            ->where('as_of_date', $asofdate)->where('is_deleted', 0)->count();

            if($list_type == 2) {
             
               $exists = DB::table('product_watchlist')->where('product_id', $product_id)
                           ->where('is_deleted', 0)->where('list_type', $list_type)->first(); 
            } else {
                $exists = DB::table('product_watchlist')->where('product_id', $product_id)
                            ->where('as_of_date', $asofdate)->where('is_deleted', 0)->where('list_type', $list_type)->first();
            }
            
            $type = !empty($exists->list_type) && isset($exists->list_type) ? Product::getWatchlistType($exists->list_type) : '';

            if (empty($exists)) {
                try {

                    DB::table('product_watchlist')->insert([
                        'product_id' => $product_id,
                        'list_type' => $list_type,
                        'as_of_date' => $asofdate,
                        'created_at' => $currentDateTime,
                        'inserted_by' => $insertedby
                    ]);

                    $scount++;
                } catch (\Exception $error) {
                    $error[] = $error->getMessage();
                }
            } else {
                $dcount++;
            }
        }

        if (!empty($error)) {
            $errorStr = implode(", ", $error);
            return $this->sendErrorResponse('Adding products to watchlist failed ', 'Failed : ' . $errorStr, 208);
        }
        if (!empty($dcount) && !empty($scount)) {
            return $this->sendResponse('Found ' . $dcount . ' duplicate product/s, some products are alredy available in the ' . $type . ' watchlist ' . $scount . ' Products is/are added to watchlist successfully', 200);
        }
        if (!empty($dcount) && empty($scount)) {
            return $this->sendResponse('These ' . $dcount . ' products are alredy available in the ' . $type . ' watchlist', 200);
        }
        if (empty($dcount) && !empty($scount)) {
            return $this->sendResponse($scount . ' Products are added to watchlist successfully', 200);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function getWatchlist($type, $page, $sortcolumn, $sort)
    {
        $pdata = [];
        $sortcolumn;
        $limit = 20;
        $sortcolumn = trim($sortcolumn);
        $today = date("Y-m-d");
        if ($sortcolumn == 'ProductAC4') {
            $sortcolumn = 'Product_AC_4';
        }
         if ($sortcolumn == 'AsOfDate') {
            $sortcolumn = 'as_of_date';
        }
        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        if ($type == 6) {
            $day = strtolower(date('l'));
        $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate', 'pw.day as Day'
                        )->where('pw.status', '=', 0)
                ->where('pw.list_type', '=', $type)
                ->where('pw.is_deleted', '=', 0)
                 ->where('pw.day', $day)
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
                ->select('p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        'pw.as_of_date as AsOfDate'
                )->where('pw.status', '=', 0)
                ->where('pw.list_type', '=', $type)
                ->where('pw.is_deleted', '=', 0)
                 ->where('pw.day', $day)
                ->count();
        } else if($type == 5) {
               $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate', 'pw.day'
                        )->where('pw.status', '=', 0)->where('pw.list_type', '=', $type)
                         ->where('pw.is_deleted', '=', 0)
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
                ->select('p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        'pw.as_of_date as AsOfDate'
                )->where('pw.status', '=', 0)->where('pw.list_type', '=', $type)->where('pw.is_deleted', '=', 0)
                ->count();
        }  else if($type == 7) {
                  $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                           ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'pw.as_of_date');
                            })
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate', 'pw.day',  'g.BrokenRule'
                        )
//                         ->where('pw.status', '=', 0)
                         ->where('pw.list_type', '=', $type)
//                              ->where('pw.as_of_date', $today)
                         ->where('pw.is_deleted', '=', 0)
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
                           ->leftjoin('undercost_lines as g', function ($join)  {
                                $join->on('p.Product_Code', '=', 'g.SPOT CODE')
                                 ->on('g.DATE', '=', 'pw.as_of_date');
                            })
//                ->where('pw.status', '=', 0)
                ->where('pw.list_type', '=', $type)
                ->where('pw.is_deleted', '=', 0)
//                     ->where('pw.as_of_date', $today)
                ->count();
        } else {
                 $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
                        ->select('pw.watchlist_id', 'p.Product_AC_4 as ProductAC4', "p.Product_Id as pid", 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                'pw.as_of_date as AsOfDate', 'pw.day'
                        )
//                         ->where('pw.status', '=', 0)
                         ->where('pw.list_type', '=', $type)
//                              ->where('pw.as_of_date', $today)
                         ->where('pw.is_deleted', '=', 0)
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
                ->select('p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        'pw.as_of_date as AsOfDate'
                )
//                ->where('pw.status', '=', 0)
                ->where('pw.list_type', '=', $type)
                ->where('pw.is_deleted', '=', 0)
//                     ->where('pw.as_of_date', $today)
                ->count();
        }

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Watchlist retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any watchlist data found']);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function searchWatchlist(Request $request, $type, $page, $sortcolumn, $sort)
    {
        $today = date("Y-m-d");
        $requestData = $request->all();
        $prodcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";
        $sdate = !empty($requestData['sdate']) && isset($requestData['sdate']) ? $requestData['sdate'] : "";
        $edate = !empty($requestData['edate']) && isset($requestData['edate']) ? $requestData['edate'] : "";

        $pdata = [];
        $limit = 20;
        $sortcolumn = trim($sortcolumn);

        if ($sortcolumn == 'ProductAC4') {
            $sortcolumn = 'Product_AC_4';
        }

        if (empty($sortcolumn)) {
            $sortcolumn = 'AsOfDate';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }



        if (!empty($prodcode) && empty($sdate) && empty($edate)) {
            $prdata = Product::searchWatchlistByProduct($type, $prodcode, $page, $sortcolumn, $sorder, $limit);
            $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
            $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        } else if (empty($prodcode) && !empty($sdate) && !empty($edate)) {
            $prdata = Product::searchWatchlistByDate($type, $sdate, $edate, $page, $sortcolumn, $sorder, $limit);
            $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
            $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        } else {
            $prdata = Product::searchWatchlistByProductDate($type, $prodcode, $sdate, $edate, $page, $sortcolumn, $sorder, $limit);
            $products = !empty($prdata['products']) && isset($prdata['products']) ? $prdata['products'] : [];
            $rowCnt = !empty($prdata['count']) && isset($prdata['count']) ? $prdata['count'] : [];
        }


        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if ($products) {
            return $this->sendResponse($pdata, 'Watchlist retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any watchlist data found']);
        }
    }

    /**
     * Deactivate tag with product 
     *
     * @param  \App\Product  $productid The Product ID
     *
     * @request tag_id , severity
     * 
     * @return json The Json array to send response
     */
    public function removeFromWatchlist($watchlistid)
    {
        $watchlistid = (int) $watchlistid;

        $lastchanged_by = Auth::user()->id;
        $currentDateTime = Carbon::now();
        try {
            $wathAvailable = DB::table('product_watchlist')->where("watchlist_id", $watchlistid)->where("is_deleted", 0)->count();

            if (!empty($wathAvailable) && $wathAvailable > 0) {
                DB::table('product_watchlist')
                        ->where(["watchlist_id" => $watchlistid])
                        ->update(['is_deleted' => 1, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

                return $this->sendResponse('Product is removed from watchlist successfully ', 200);
            } else {
                return $this->sendErrorResponse([], 'Update watchlist failed : Watchlist item is not found', 208);
            }
        } catch (\Exception $error) {
            return $this->sendErrorResponse([], 'Update watchlist failed : ' . $error->getMessage(), 208);
        }
    }

    public function getWatchlistCatalog($page, $sortcolumn, $sort)
    {
        $fday = date('Y-m-01');

        $pdata = [];
        $sortcolumn;
        $limit = 10;
        $sortcolumn = trim($sortcolumn);
        $today = date("Y-m-d");
        $latestpriceReview = CompetitorPricing::select("AsOfDate")->orderBy("AsOfDate", "DESC")->first();
        $sDate = !empty($latestpriceReview->AsOfDate) && isset($latestpriceReview->AsOfDate) ? $latestpriceReview->AsOfDate : $today;
//        $sDate = '2023-12-11';
        $eDate = '2023-12-16';

        if ($sortcolumn == 'ProductAC4') {
            $sortcolumn = 'Product_AC_4';
        }
        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }


        $products = DB::table('DwProduct as p')
//                        ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                        })->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                        ->select('p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                                DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                'i.Average_usage as Avg Vol',
                                DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                                DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                                DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
//                                DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
//                                DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
//                                DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
//                                DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
//                                DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Bestway"),
                                'cp.phoenix_outofstock', 'cp.trident_outofstock', 'cp.aah_outofstock', 'cp.colorama_outofstock', 'cp.bestway_outofstock',
                                'cp.AsOfDate'
                        )
                        ->where('p.Product_AC_5', '=', 'SPOT')
                        ->where('p.Product_AC_1', '=', 'GENERI')
                        ->where('Product_AC_4', 'not like', '%***%')
//                        ->whereNotNull("cp.phoenix ")
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = Product::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;

            return $products;
        });

        $rowCnt = DB::table('DwProduct as p')
//                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                })->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->where('p.Product_AC_5', '=', 'SPOT')
                ->where('p.Product_AC_1', '=', 'GENERI')
                ->where('Product_AC_4', 'not like', '%***%')
//                ->whereNotNull("cp.phoenix ")
                ->count();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product list retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any products data found']);
        }
    }

    public function getBuyerShortlisted($page, $sortcolumn, $sort)
    {

        $fday = date('Y-m-01');

        $pdata = [];
        $sortcolumn;
        $limit = 10;
        $sortcolumn = trim($sortcolumn);
        $today = date("Y-m-d");

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }


        $products = DB::table('DwProduct as p')
                        ->join(DB::raw("(SELECT product_id FROM supplier_product_comments where cast([created_at] as date) ='" . $today . "' group by product_id)
               spc"),
                                function ($join) {
                                    $join->on('spc.product_id', '=', 'p.Product_Id');
                                })
//                          ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                           ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                        // ->leftjoin("vw_lastest_competitor_prices as cp", 'p.Product_Id', '=', 'cp.product_id')
                        ->leftjoin('competitor_prices as cp', function ($join) {
                            $join->on('p.Product_Id', '=', 'cp.product_id')
                            ->whereRaw(
                                    'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
                            );
                        })
                        ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                        ->select('p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                                DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                'i.Average_usage as Avg Vol',
                                DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                                DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                                DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
//                                DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
//                                DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
//                                DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
//                                DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
//                                DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                                
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Bestway"),
                                
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.AsOfDate'
                        )
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $scommentsStr = Product::getSupplierComment($products->ProductAC4);
            $products->supplier_comments = $scommentsStr;

            return $products;
        });

        $rowCnt = DB::table('DwProduct as p')
                ->join(DB::raw("(SELECT product_id FROM supplier_product_comments where cast([created_at] as date) ='" . $today . "' group by product_id)
               spc"),
                        function ($join) {
                            $join->on('spc.product_id', '=', 'p.Product_Id');
                        })
//                    ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                        
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
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->count();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product list retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any products data found']);
        }
    }

    public function searchWatchlistCatalog(Request $request, $page, $sortcolumn, $sort)
    {

        $fday = date('Y-m-01');

        $pdata = [];
        $requestData = $request->all();
        $prodcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";
        $isbuyercomment = !empty($requestData['isbuyercomment']) && isset($requestData['isbuyercomment']) ? $requestData['isbuyercomment'] : "";
        $limit = 10;
        $sortcolumn = trim($sortcolumn);
        $today = date("Y-m-d");

        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }


        if ($isbuyercomment == 1) {
            
        } else {
            
        }

        if ($isbuyercomment == 1) {

            $products = DB::table('DwProduct as p')
                            ->join(DB::raw("(SELECT product_id FROM supplier_product_comments where cast([created_at] as date) ='" . $today . "' group by product_id)
               spc"),
                                    function ($join) {
                                        $join->on('spc.product_id', '=', 'p.Product_Id');
                                    })
//                                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                    'i.Average_usage as Avg Vol',
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                    DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                    DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                    DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                    DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                                    DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                    DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                                    DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
//                                    DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
//                                    DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
//                                    DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
//                                    DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
//                                    DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Bestway"),
                                    'phoenix_outofstock',
                                    'trident_outofstock',
                                    'aah_outofstock',
                                    'colorama_outofstock',
                                    'bestway_outofstock',
                                    'cp.AsOfDate'
                            )->where(function ($query) use ($prodcode) {
                                $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                            })
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = Product::getBuyerComment($products->Product_Id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = Product::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;

                return $products;
            });

            $rowCnt = DB::table('DwProduct as p')
                    ->join(DB::raw("(SELECT product_id FROM supplier_product_comments where cast([created_at] as date) ='" . $today . "' group by product_id)
               spc"),
                            function ($join) {
                                $join->on('spc.product_id', '=', 'p.Product_Id');
                            })
//                        ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->where(function ($query) use ($prodcode) {
                        $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                    })
                    ->count();
        } else {


            $products = DB::table('DwProduct as p')
                            ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                            ->leftjoin('competitor_prices as cp', function ($join) {
                                $join->on('p.Product_Id', '=', 'cp.product_id')
                                ->whereRaw(
                                        'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
                                );
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                    DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                                    DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                    'i.Average_usage as Avg Vol',
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                    DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                    DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                    DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                    DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                                    DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                    DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                                    DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
//                                    DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
//                                    DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
//                                    DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
//                                    DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
//                                    DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                                    
                                    DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Bestway"),
                                    'phoenix_outofstock',
                                    'trident_outofstock',
                                    'aah_outofstock',
                                    'colorama_outofstock',
                                    'bestway_outofstock',
                                    'cp.AsOfDate'
                            )
                            ->where('p.Product_AC_5', '=', 'SPOT')
                            ->where('p.Product_AC_1', '=', 'GENERI')
                            ->where('Product_AC_4', 'not like', '%***%')
                            ->where(function ($query) use ($prodcode) {
                                $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                            })
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
                $commentsStr = Product::getBuyerComment($products->Product_Id);
                $products->buyer_comments = $commentsStr;
                $scommentsStr = Product::getSupplierComment($products->ProductAC4);
                $products->supplier_comments = $scommentsStr;

                return $products;
            });
            $rowCnt = DB::table('DwProduct as p')
                    ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
                    ->leftjoin('competitor_prices as cp', function ($join) {
                        $join->on('p.Product_Id', '=', 'cp.product_id')
                        ->whereRaw(
                                'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
                        );
                    })
                    ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                    ->where('p.Product_AC_5', '=', 'SPOT')
                    ->where('p.Product_AC_1', '=', 'GENERI')
                    ->where('Product_AC_4', 'not like', '%***%')
                    ->where(function ($query) use ($prodcode) {
                        $query->where('p.Product_AC_4', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Code', 'like', '%' . $prodcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $prodcode . '%');
                    })
                    ->count();
        }


        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product list retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any products data found']);
        }
    }
    
     /**

     * Create User Api Accept all the required field and store user in Database and send Email Notification
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json Store The User information in database
     *
     */
    public function notifyReviewer(Request $request)
    {
        //Server side validations
        $requestData = $request->all();
        $name = $email = '';

        //Gets request data
        $type = !empty($requestData['type']) && isset($requestData['type']) ? $requestData['type'] : "";
        $list_type = !empty($requestData['list_type']) && isset($requestData['list_type']) ? $requestData['list_type'] : "";
        $lines = !empty($requestData['dataCount']) && isset($requestData['dataCount']) ? $requestData['dataCount'] : "";
        try {
            if (!empty($lines)) {
            $txtmessage = 'Added products for review';
            if (($type == 1 || $type == 2) && $list_type != 3) {
                $name = 'Reviewer, ';
                $subject = 'For Sigma Reviewer - Added products for review';
               $email = ['jaylesh.s@sigmaplc.com', 'anand.m@sigmaplc.com', 'sushant@webdezign.co.uk'];
            } else if ($list_type == 3 && $type != 4) {
                 $email = ['sushant@webdezign.co.uk', 'ashwini@webdezign.co.uk'];
                $name = 'India Review Team, ';
                $subject = 'For India Review Team - Added products for review';
            } else if ($type == 4 && ($list_type == 1 || $list_type == 4 || $list_type == 3 || $list_type == 2 || $list_type == 6 || $list_type == 7)) {
                 //$email = ['shrenal@sigmaplc.com','sushant@webdezign.co.uk','anand.m@sigmaplc.com'];
                 $email = ['sushant@webdezign.co.uk'];
                $name = 'Pricer, ';
                $txtmessage = 'Competitor Price review is done';
                 $subject = 'For Pricer - Competitor Price review is done';
            } else {
                return $this->sendErrorResponse([], 'Failed : Could not send email to user', 204);
            }


            $data = ["name" => $name, "msg" => $txtmessage];
            if($list_type == 1 || $list_type == 3 || $list_type == 4 || $list_type == 7) {
            $wsuccess = DB::table('product_watchlist')
                    ->where(["list_type" => $list_type])
                    ->update(["status" => 1]);
            }
            
             $success = DB::table('competitor_prices')
                    ->where(["group" => $list_type])
                    ->update(["actioned" => 1]);

            if (!empty($success)) {
                \Mail::send('emails.notify_reviewer', ['data' => $data], function ($message) use ($email, $txtmessage, $subject) {
                    $message->to($email)
                           // ->cc('shrenal@sigmaplc.com', 'Shrenal Patel')
                            ->cc('sushant@webdezign.co.uk', 'Sushant Chari')
                            ->subject($subject);
                });
                return $this->sendResponse([], 'Notification mail sent successfully.');
            } else {
                return $this->sendResponse([], 'Failed to updated lines as actioned.');
            }
            } else {
                return $this->sendResponse([], 'No any data available');
            }
            
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Could not send email to user', 'Failed : ' . $error->getMessage(), 204);
        }
    }

    /**

     * Create User Api Accept all the required field and store user in Database and send Email Notification
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json Store The User information in database
     *
     */
    public function notifyReviewer1(Request $request)
    {
        //Server side validations
        $requestData = $request->all();
        $name = $email = '';

        //Gets request data
        $type = !empty($requestData['type']) && isset($requestData['type']) ? $requestData['type'] : "";
        $list_type = !empty($requestData['list_type']) && isset($requestData['list_type']) ? $requestData['list_type'] : "";
        $lines = !empty($requestData['dataCount']) && isset($requestData['dataCount']) ? $requestData['dataCount'] : "";
        try {
            if (!empty($lines)) {
            $txtmessage = 'Added products for review';
            if (($type == 1 || $type == 2) && $list_type != 3) {
                $name = 'Reviewer, ';
                $subject = 'For Sigma Reviewer - Added products for review';
               $email = ['jaylesh.s@sigmaplc.com', 'anand.m@sigmaplc.com', 'sushant@webdezign.co.uk'];
            } else if ($list_type == 3 && $type != 4) {
                 $email = ['sushant@webdezign.co.uk', 'ashwini@webdezign.co.uk'];
                $name = 'India Review Team, ';
                $subject = 'For India Review Team - Added products for review';
            } else if ($type == 4 && ($list_type == 1 || $list_type == 4 || $list_type == 3 || $list_type == 2 || $list_type == 6 || $list_type == 7)) {
//                 $email = ['shrenal@sigmaplc.com','sushant@webdezign.co.uk'];
                 $email = ['sushant@webdezign.co.uk'];
                $name = 'Shrenal Patel, ';
                $txtmessage = 'Competitor Price review is done';
                 $subject = 'For Pricer - Competitor Price review is done';
            } else {
                return $this->sendErrorResponse([], 'Failed : Could not send email to user', 204);
            }


            $data = ["name" => $name, "msg" => $txtmessage];
            if($list_type == 1 || $list_type == 3 || $list_type == 4 || $list_type == 7) {
            $wsuccess = DB::table('product_watchlist')
                    ->where(["list_type" => $list_type])
                    ->update(["status" => 1]);
            }
            
             $success = DB::table('competitor_prices')
                    ->where(["group" => $list_type])
                    ->update(["actioned" => 1]);

            if (!empty($success)) {
                \Mail::send('emails.notify_reviewer', ['data' => $data], function ($message) use ($email, $txtmessage, $subject) {
                    $message->to($email)
//                            ->cc('shrenal@sigmaplc.com', 'Shrenal Patel')
                            //->cc('sushant@webdezign.co.uk', 'Sushant Chari')
                            ->subject($subject);
                });
                return $this->sendResponse([], 'Notification mail sent successfully.');
            } else {
                return $this->sendResponse([], 'Failed to updated lines as actioned.');
            }
            } else {
                return $this->sendResponse([], 'No any data available');
            }
            
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Could not send email to user', 'Failed : ' . $error->getMessage(), 204);
        }
    }

    public function getPricingList($type, $page, $sortcolumn, $sort)
    {

        $fday = date('Y-m-01');
       
        $pdata = [];
        $sortcolumn;
        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if ($sortcolumn == 'ProductAC4') {
            $sortcolumn = 'Product_AC_4';
        }
        $today = date("Y-m-d");
        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
          if ($type == 1 || $type == 4 || $type == 2) {
                  $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                          ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
//                       
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                 DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                 DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                              //  DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                //DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                //DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                               // DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                //DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                //DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                //DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                //DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage'
                        )
                        ->where('cp.group', '=', $type)
                              ->where('cp.actioned', 1)  
//                                  ->where('pw.as_of_date', $today)
//                            ->where('pw.is_deleted', 0)
//                            ->where('pw.status', '=', 0)
//                 ->whereNull("g.price_until_date")
//                ->where(function ($query) use ($yesterday) {
//                    $query->whereNull("g.price_until_date")->orWhere('g.price_until_date', $yesterday);
//                })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });

        $rowCnt = DB::table('competitor_prices as cp')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
               ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
//                ->leftjoin('competitor_prices as cp', function ($join) {
//                    $join->on('p.Product_Id', '=', 'cp.product_id')
//                    ->whereRaw(
//                            'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
//                    );
//                })
               ->where('cp.group', '=', $type)
//                ->where('pw.is_deleted', 0)
//                ->where('pw.as_of_date', $today)
//                ->where('pw.status', '=', 0)
                          ->where('cp.actioned', 1)  
                ->count(); 
         }  else if ($type == 7) {
              $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                       ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                          ->leftjoin('undercost_lines as u', function ($join) {
                                $join->on('p.Product_Code', '=', 'u.SPOT CODE')
                                        ->on('u.DATE', '=', 'cp.AsOfDate');
                             })
//                       
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                               DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                              //  DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                  DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                //DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                //DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                               // DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                //DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                //DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                //DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                //DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage',
                                'u.BrokenRule'
                        )
                        ->where('cp.group', '=', $type)
                              ->where('cp.actioned', 1)  
//                                  ->where('pw.as_of_date', $today)
//                            ->where('pw.is_deleted', 0)
//                            ->where('pw.status', '=', 0)
//                 ->whereNull("g.price_until_date")
//                ->where(function ($query) use ($yesterday) {
//                    $query->whereNull("g.price_until_date")->orWhere('g.price_until_date', $yesterday);
//                })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });

        $rowCnt = DB::table('competitor_prices as cp')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                          ->leftjoin('undercost_lines as u', function ($join) {
                                $join->on('p.Product_Code', '=', 'u.SPOT CODE')
                                        ->on('u.DATE', '=', 'cp.AsOfDate');
                             })
               ->where('cp.group', '=', $type)
                          ->where('cp.actioned', 1)  
                ->count(); 
         } else if ($type == 3) {
                $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                          ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
//                        ->leftjoin('competitor_prices as cp', function ($join) {
//                            $join->on('p.Product_Id', '=', 'cp.product_id')
//                            ->whereRaw(
//                                    'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
//                            );
//                        })
//                        ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                    DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
//                                DB::raw('CAST(g.cost AS DECIMAL(10,2)) AS Cost'),
//                                DB::raw('CAST(g.true_cost AS DECIMAL(10,2)) AS "True Cost"'),
                                DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS 'True Cost'"),
//                                DB::raw('CAST(g.avg_cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS 'Avg Cost'"),
                                'g.avg_volume as Avg Vol',
                                DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
//                                DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
//                                DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage'
                        )->where('cp.group', '=', $type)
                ->where('cp.AsOfDate', $today)
//                ->where('pw.status', '=', 0)
                    ->where('cp.actioned', 1) 
//                ->where('pw.is_deleted', 0)
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });

        $rowCnt = DB::table('competitor_prices as cp')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
               ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
//                ->leftjoin('competitor_prices as cp', function ($join) {
//                    $join->on('p.Product_Id', '=', 'cp.product_id')
//                    ->whereRaw(
//                            'cp.cprice_id = (SELECT MAX(cprice_id) FROM competitor_prices WHERE product_id = p.Product_Id)'
//                    );
//                })
                        ->where('cp.group', '=', $type)
                ->where('cp.AsOfDate', $today)
//                ->where('pw.status', '=', 0)
                    ->where('cp.actioned', 1)
                ->count(); 
         } else if ($type == 6) {
               $day = strtolower(date('l'));
               $products = DB::table('product_watchlist as pw')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
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
                        ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                        ->select('pw.watchlist_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                                DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                                DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                                'i.Average_usage as Avg Vol',
                                DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
//                                DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
//                                DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
                                DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
                                DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
                                DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
                                DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'pw.as_of_date as AsOfDate',
                                'pw.list_type as group',
                                'g.shortage'
                        )->where('pw.list_type', '=', $type)
                            ->where('pw.is_deleted', 0)
                            ->where('pw.day', $day)
                            ->where('pw.status', '=', 1)
                                 ->where('cp.actioned', 1)
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });

        $rowCnt = DB::table('product_watchlist as pw')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")
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
                })->where('pw.list_type', '=', $type)
                            ->where('pw.is_deleted', 0)
                            ->where('pw.day', $day)
                            ->where('cp.actioned', 1)
                            ->where('pw.status', '=', 1)
                ->count(); 
         } elseif ($type == 7) {
             $today = date("Y-m-d");
//                $today = '2024-01-17';
               $products = DB::table('dbo.undercost_lines as ul')
                            ->leftjoin("DwProduct as p", "p.Product_Code", "=", "ul.product_code")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($type) {
                                 $join->on('cp.product_id', '=', 'p.Product_Id')
                                ->on('ul.product_code', '=', 'p.Product_code')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$type'"));
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                            ->select('ul.gprice_id as watchlist_id', 'g.ranking as Ranking', 'p.Product_Id as product_id',
                                    'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 
                                    'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                     DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS 'True Cost'"),
                                 DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS 'Avg Cost'"),
                                'g.avg_volume as Avg Vol',
                                    DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                                DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                                DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                                DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                                DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
                                DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
                                DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
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
                                    'cp.AsOfDate',
                                    'cp.group',
                                    'g.shortage'
                            )
                           
                            ->where('ul.asofdate', $today)
                            ->orderBy($sortcolumn, $sorder)
                            ->limit($limit)->offset(($page - 1) * $limit)
                            ->get()->map(function ($products) {
               
                $commentsStr = Product::getBuyerComment($products->product_id);
                $products->buyer_comments = $commentsStr;
                $prCommentsStr = Product::getPricierComment($products->product_id);
                $products->pricier_comments = $prCommentsStr;
               
                return $products;
            });

            $rowCnt = DB::table('dbo.undercost_lines as ul')
                            ->leftjoin("DwProduct as p", "p.Product_Code", "=", "ul.product_code")
                            ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                            ->leftjoin('competitor_prices as cp', function ($join) use ($type) {
                                 $join->on('cp.product_id', '=', 'p.Product_Id')
                                ->on('ul.product_code', '=', 'p.Product_code')
                                ->on("cp.actioned", DB::raw("'0'"))
                                ->on("cp.group", DB::raw("'$type'"));
                            })
                            ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                   ->where('ul.asofdate', $today)
                    ->count();
        
        
     }
  

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product list retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any products data found']);
        }
    }

    public function searchPricingList(Request $request, $type, $page, $sortcolumn, $sort)
    {

        $requestData = $request->all();
        $pcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";
        $limit = 10;
        $sortcolumn = trim($sortcolumn);

        if ($sortcolumn == 'ProductAC4') {
            $sortcolumn = 'Product_AC_4';
        }

        $today = date("Y-m-d");
        if (empty($sortcolumn)) {
            $sortcolumn = 'Product_AC_4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }
       if ($type == 7) {
              $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                       ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                          ->leftjoin('undercost_lines as u', function ($join) {
                                $join->on('p.Product_Code', '=', 'u.SPOT CODE')
                                        ->on('u.DATE', '=', 'cp.AsOfDate');
                             })
//                       
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                               DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                    DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                              //  DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                  DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                //DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                //DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                               // DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                //DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                //DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                //DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                //DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage',
                                'u.BrokenRule'
                        )
                        ->where('cp.group', '=', $type)
                              ->where('cp.actioned', 1)
                            ->where(function ($query) use ($pcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $pcode . '%')->orWhere('p.Product_Code', 'like', '%' . $pcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $pcode . '%');
                        })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;

            return $products;
        });

        $rowCnt = DB::table('competitor_prices as cp')
                ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
                          ->leftjoin('undercost_lines as u', function ($join) {
                                $join->on('p.Product_Code', '=', 'u.SPOT CODE')
                                        ->on('u.DATE', '=', 'cp.AsOfDate');
                             })
               ->where('cp.group', '=', $type)
                          ->where('cp.actioned', 1)
                        ->where(function ($query) use ($pcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $pcode . '%')->orWhere('p.Product_Code', 'like', '%' . $pcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $pcode . '%');
                        })
                ->count(); 
         } else {
        $products = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                          ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
//                       
                        ->select('cp.cprice_id as gprice_id', 'p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                                DB::raw("REPLACE(REPLACE(CAST(g.cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Cost"),
                                DB::raw("REPLACE(REPLACE(CAST(g.true_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS TrueCost"),
                                DB::raw("REPLACE(REPLACE(CAST(g.avg_cost AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgCost"),
                                DB::raw("REPLACE(REPLACE(CAST(g.avg_volume AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AvgVol"),
                                DB::raw("REPLACE(REPLACE(CAST(g.c87 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c87"),
                              //  DB::raw('CAST(g.new_c87 AS DECIMAL(10,2)) AS new_c87'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.c122 AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS c122"),
                                //DB::raw('CAST(g.new_c122 AS DECIMAL(10,2)) AS new_c122'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DC AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DC"),
                                //DB::raw('CAST(g.new_DC AS DECIMAL(10,2)) AS new_DC'),
                                DB::raw("REPLACE(REPLACE(CAST(g.DG AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS DG"),
                               // DB::raw('CAST(g.new_DG AS DECIMAL(10,2)) AS new_DG'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RH AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RH"),
                                //DB::raw('CAST(g.new_RH AS DECIMAL(10,2)) AS new_RH'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RBS"),
                                //DB::raw('CAST(g.new_RBS AS DECIMAL(10,2)) AS new_RBS'),
                                 DB::raw("REPLACE(REPLACE(CAST(g.ATOZ AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS ATOZ"),
                                //DB::raw('CAST(g.new_ATOZ AS DECIMAL(10,2)) AS new_ATOZ'),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS RRP"),
                                //DB::raw('CAST(g.new_RRP AS DECIMAL(10,2)) AS new_RRP'),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Phoenix"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Trident"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS AAH"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Colorama"),
                                   DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '0.00', '0'),'00','0')  AS Bestway"),
                                'phoenix_outofstock',
                                'trident_outofstock',
                                'aah_outofstock',
                                'colorama_outofstock',
                                'bestway_outofstock',
                                'cp.AsOfDate',
                                'cp.group',
                                'g.shortage'
                        )
                        ->where('cp.group', '=', $type)
                        ->where('cp.actioned', 1) 
                        ->where(function ($query) use ($pcode) {
                            $query->where('p.Product_AC_4', 'like', '%' . $pcode . '%')->orWhere('p.Product_Code', 'like', '%' . $pcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $pcode . '%');
                        })
                        ->orderBy($sortcolumn, $sorder)
                        ->limit($limit)->offset(($page - 1) * $limit)
                        ->get()->map(function ($products) {
            $commentsStr = Product::getBuyerComment($products->Product_Id);
            $products->buyer_comments = $commentsStr;
            $prCommentsStr = Product::getPricierComment($products->Product_Id);
            $products->pricier_comments = $prCommentsStr;
            return $products;
        });

        $rowCnt = DB::table('competitor_prices as cp')
                        ->leftjoin("dbo.DwProduct as p", "p.Product_Id", "=", "cp.product_id")
                          ->leftjoin('group_pricing as g', function ($join) {
                                $join->on('p.Product_Code', '=', 'g.product_code')
                                ->whereRaw(
                                        'g.gprice_id = (SELECT MAX(gprice_id) FROM group_pricing WHERE product_code = p.Product_Code)'
                                );
                            })
               ->where('cp.group', '=', $type)
                ->where('cp.actioned', 1) 
                ->where(function ($query) use ($pcode) {
                    $query->where('p.Product_AC_4', 'like', '%' . $pcode . '%')->orWhere('p.Product_Code', 'like', '%' . $pcode . '%')->orWhere('p.Product_Desc', 'like', '%' . $pcode . '%');
                })
                ->count();
         }
        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Product list retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any products data found']);
        }
    }

    /**
     * Updates the Sigma Contract pricing by Pricier
     *
     * @param \Illuminate\Http\Request $request Request with contract prices
     *
     * @return json The Json response of result
     */
    public function updateContractPricing(Request $request)
    {

        try {

            $requestData = $request->all();
            //Gets request data
            $currentDateTime = Carbon::now();
            //Today
            $currentDate = date('Y-m-d');
            //Yesterday
            $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));

            $inserted_by = $lastchanged_by = Auth::user()->id;

            $gprice_id = !empty($requestData['gprice_id']) && isset($requestData['gprice_id']) ? trim($requestData['gprice_id']) : "";
            $new_RRP = !empty($requestData['new_RRP']) && isset($requestData['new_RRP']) ? trim($requestData['new_RRP']) : NULL;
            $new_ATOZ = !empty($requestData['new_ATOZ']) && isset($requestData['new_ATOZ']) ? trim($requestData['new_ATOZ']) : NULL;
            $new_c87 = !empty($requestData['new_c87']) && isset($requestData['new_c87']) ? trim($requestData['new_c87']) : NULL;
            $new_c122 = !empty($requestData['new_c122']) && isset($requestData['new_c122']) ? trim($requestData['new_c122']) : NULL;
            $new_DC = !empty($requestData['new_DC']) && isset($requestData['new_DC']) ? trim($requestData['new_DC']) : NULL;
            $new_DG = !empty($requestData['new_DG']) && isset($requestData['new_DG']) ? trim($requestData['new_DG']) : NULL;
            $new_RH = !empty($requestData['new_RH']) && isset($requestData['new_RH']) ? trim($requestData['new_RH']) : NULL;
            $new_RBS = !empty($requestData['new_RBS']) && isset($requestData['new_RBS']) ? trim($requestData['new_RBS']) : NULL;

            $pdata = DB::table('product_watchlist as pw')
                            ->join("dbo.DwProduct as p", "p.Product_Id", "=", "pw.product_id")->select('Product_AC_4', 'Product_Code')->where(["watchlist_id" => $gprice_id])->first();
            $agg_code = !empty($pdata->Product_AC_4) && isset($pdata->Product_AC_4) ? trim($pdata->Product_AC_4) : "";
            $product_code = !empty($pdata->Product_Code) && isset($pdata->Product_Code) ? trim($pdata->Product_Code) : "";
            $gpricingOld = GroupPricing::where(["agg_code" => $agg_code, "product_code" => $product_code, "asofdate" => $yesterday])->first();
            $gpriceIdOld = !empty($gpricingOld->gprice_id) && isset($gpricingOld->gprice_id) ? trim($gpricingOld->gprice_id) : "";
            $gpricingA = GroupPricing::where(["agg_code" => $agg_code, "product_code" => $product_code])
                            ->whereNull("asofdate")->first();
            $gpriceIdA = !empty($gpricingA->gprice_id) && isset($gpricingA->gprice_id) ? trim($gpricingA->gprice_id) : "";

            if (!empty($gpriceIdA) && empty($gpriceIdOld)) {
                $contractP = GroupPricing::find($gpriceIdA);
                $newContractP = $contractP->replicate();
                $newContractP->new_RRP = $new_RRP;
                $newContractP->new_ATOZ = $new_ATOZ;
                $newContractP->new_c87 = $new_c87;
                $newContractP->new_c122 = $new_c122;
                $newContractP->new_DC = $new_DC;
                $newContractP->new_DG = $new_DG;
                $newContractP->new_RH = $new_RH;
                $newContractP->new_RBS = $new_RBS;
                $newContractP->asofdate = $currentDate;
                $newContractP->created_at = $currentDateTime;
                $newContractP->inserted_by = $inserted_by;
                $newContractP->save();

                //Updates the price_untill_date of old/exiting/historical record
//                DB::table('group_pricing')
//                        ->where(["gprice_id" => $gpriceIdA])
//                        ->update(['new_RRP' => $new_RRP, 'new_ATOZ' => $new_ATOZ, 'new_c87' => $new_c87, 'new_c122' => $new_c122,
//                            'new_DC' => $new_DC, 'new_DG' => $new_DG, 'new_RH' => $new_RH, 'new_RBS' => $new_RBS, 'price_until_date' => $yesterday, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
            } else {
                DB::table('group_pricing')
                        ->where(["gprice_id" => $gpriceIdOld])
                        ->update(['new_RRP' => $new_RRP, 'new_ATOZ' => $new_ATOZ, 'new_c87' => $new_c87, 'new_c122' => $new_c122,
                            'new_DC' => $new_DC, 'new_DG' => $new_DG, 'new_RH' => $new_RH, 'new_RBS' => $new_RBS, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
                DB::table('group_pricing')
                        ->where(["gprice_id" => $gpriceIdA])
                        ->update(['new_RRP' => $new_RRP, 'new_ATOZ' => $new_ATOZ, 'new_c87' => $new_c87, 'new_c122' => $new_c122,
                            'new_DC' => $new_DC, 'new_DG' => $new_DG, 'new_RH' => $new_RH, 'new_RBS' => $new_RBS, 'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);
            }





            return $this->sendResponse('Contract Price Updated', 'Contract price updated successfully.');
        } catch (\Exception $error) {
            return $this->sendErrorResponse([], 'Failed to update price ' . $error->getMessage(), 208);
        }
    }

    /**
     * Product API which connects to Azure SQL Database and return json array of Live products
     *
     * @param integer $page The Page Number
     *
     * @return json The product Items json array
     */
    public function downloadProductCatalog()
    {
        $today = date("Y-m-d");
        $products = DB::table('DwProduct as p')
//                ->leftjoin("vw_sigma_group_pricing as g", 'p.Product_Code', '=', 'g.product_code')
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
                ->leftjoin("vw_Inventory as i", "p.Product_Id", "=", "i.Product_Id")
                ->select('p.Product_Id', 'g.ranking as Ranking', 'p.Product_AC_4 as ProductAC4', 'p.Product_Code as SPOTCode', 'p.Product_Desc as ProductDesc', 'p.Pack_Size as PackSize',
                        DB::raw('CAST(p.Standard_Cost AS DECIMAL(10,2)) AS Cost'),
                        DB::raw('CAST(i.True_Cost AS DECIMAL(10,2)) AS "True Cost"'),
                        DB::raw('CAST(i.Avg_Cost AS DECIMAL(10,2)) AS "Avg Cost"'),
                        'i.Average_usage as Avg Vol',
                        DB::raw('CAST(g.c87 AS DECIMAL(10,2)) AS c87'),
                        DB::raw('CAST(g.c122 AS DECIMAL(10,2)) AS c122'),
                        DB::raw('CAST(g.DC AS DECIMAL(10,2)) AS DC'),
                        DB::raw('CAST(g.DG AS DECIMAL(10,2)) AS DG'),
                        DB::raw('CAST(g.RH AS DECIMAL(10,2)) AS RH'),
//                        DB::raw('CAST(g.RBS AS DECIMAL(10,2)) AS RBS'),
                        DB::raw('CAST(g.ATOZ AS DECIMAL(10,2)) AS ATOZ'),
//                        DB::raw('CAST(g.RRP AS DECIMAL(10,2)) AS RRP'),
//                        DB::raw('CAST(cp.phoenix AS DECIMAL(10,2)) AS Phoenix'),
//                        DB::raw('CAST(cp.trident AS DECIMAL(10,2)) AS Trident'),
//                        DB::raw('CAST(cp.aah AS DECIMAL(10,2)) AS AAH'),
//                        DB::raw('CAST(cp.colorama AS DECIMAL(10,2)) AS Colorama'),
//                        DB::raw('CAST(cp.bestway AS DECIMAL(10,2)) AS Bestway'),
                        DB::raw("REPLACE(REPLACE(CAST(g.RBS AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RBS"),
                                DB::raw("REPLACE(REPLACE(CAST(g.RRP AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS RRP"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.phoenix AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Phoenix"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.trident AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Trident"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.aah AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS AAH"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.colorama AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Colorama"),
                                DB::raw("REPLACE(REPLACE(CAST(cp.bestway AS DECIMAL(10,2)), '.00', '0'),'00','0')  AS Bestway"),
                        'cp.phoenix_outofstock', 'cp.trident_outofstock', 'cp.aah_outofstock', 'cp.colorama_outofstock', 'cp.bestway_outofstock',
                        'g.ranking as buyer_comments', 'p.Product_AC_4 as supplier_comments'
                )
                ->where('p.Product_AC_5', '=', 'SPOT')
                ->where('p.Product_AC_1', '=', 'GENERI')
                ->where('Product_AC_4', 'not like', '%***%')
                ->orderBy('p.Product_AC_4')
                ->get();

//         ->map(function ($products) {
//            $commentsStr = Product::getBuyerComment($products->Product_Id);
//            $products->buyer_comments = $commentsStr;
//            $scommentsStr = Product::getSupplierComment($products->ProductAC4);
//            $products->supplier_comments = $scommentsStr;
//            return $products;
//        });
//        dd($products);
        if (count($products) > 0) {
            return $this->sendResponse($products, 'Product catalog downloaded successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any product pricing data found']);
        }
    }

    public function getRunrate($page, $sortcolumn, $sort)
    {

        $pdata = [];
        $sortcolumn;
        $limit = 20;
        $sortcolumn = trim($sortcolumn);
        if (empty($sortcolumn)) {
            $sortcolumn = 'ac4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        $products = DB::table('runrate as r')
                ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                        'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                        'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                        'r.asofdate'
                )->whereNotNull('r.product_id')
                ->orderBy($sortcolumn, $sorder)
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get();

        $rowCnt = DB::table('runrate as r')
                ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                ->whereNotNull('r.product_id')
                ->count();

        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Runrate retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Runrate data found']);
        }
    }

    public function searchRunrate(Request $request, $page, $sortcolumn, $sort)
    {

        $requestData = $request->all();
        $year = !empty($requestData['year']) && isset($requestData['year']) ? $requestData['year'] : "";
        $month = !empty($requestData['month']) && isset($requestData['month']) ? $requestData['month'] : "";
        $pcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";

        $pdata = [];
        $sortcolumn;
        $limit = 20;
        $sortcolumn = trim($sortcolumn);
        if (empty($sortcolumn)) {
            $sortcolumn = 'ac4';
            $sorder = "ASC";
        } else {
            if ($sort == 1) {
                $sorder = "ASC";
            } else {
                $sorder = "DESC";
            }
        }

        if (!empty($year) && !empty($month) && empty($pcode)) {
            $products = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                            'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                            'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                            'r.asofdate'
                    )->whereNotNull('r.product_id')
                    ->where(DB::raw('YEAR(r.asofdate)'), $year)
                    ->where(DB::raw('MONTH(r.asofdate)'), $month)
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $rowCnt = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->whereNotNull('r.product_id')
                    ->where(DB::raw('YEAR(r.asofdate)'), $year)
                    ->where(DB::raw('MONTH(r.asofdate)'), $month)
                    ->count();
        } else if (empty($year) && empty($month) && !empty($pcode)) {
            $products = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                            'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                            'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                            'r.asofdate'
                    )->whereNotNull('r.product_id')
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.ac4', 'like', '%' . $pcode . '%')->orWhere('p.product_code', 'like', '%' . $pcode . '%')->orWhere('p.product_desc', 'like', '%' . $pcode . '%');
                    })
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $rowCnt = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->whereNotNull('r.product_id')
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.ac4', 'like', '%' . $pcode . '%')->orWhere('p.product_code', 'like', '%' . $pcode . '%')->orWhere('p.product_desc', 'like', '%' . $pcode . '%');
                    })
                    ->count();
        } else if (!empty($year) && !empty($month) && !empty($pcode)) {
            $products = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                            'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                            'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                            'r.asofdate'
                    )->whereNotNull('r.product_id')
                    ->where(DB::raw('YEAR(r.asofdate)'), $year)
                    ->where(DB::raw('MONTH(r.asofdate)'), $month)
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.ac4', 'like', '%' . $pcode . '%')->orWhere('p.product_code', 'like', '%' . $pcode . '%')->orWhere('p.product_desc', 'like', '%' . $pcode . '%');
                    })
                    ->orderBy($sortcolumn, $sorder)
                    ->limit($limit)->offset(($page - 1) * $limit)
                    ->get();

            $rowCnt = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->whereNotNull('r.product_id')
                    ->where(DB::raw('YEAR(r.asofdate)'), $year)
                    ->where(DB::raw('MONTH(r.asofdate)'), $month)
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.ac4', 'like', '%' . $pcode . '%')->orWhere('p.product_code', 'like', '%' . $pcode . '%')->orWhere('p.product_desc', 'like', '%' . $pcode . '%');
                    })
                    ->count();
        }


        $pdata["products"] = $products;
        $pdata["rowCnt"] = $rowCnt;
        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Runrate retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Runrate data found']);
        }
    }

    public function downloadRunrate(Request $request)
    {

        $requestData = $request->all();
        $year = !empty($requestData['year']) && isset($requestData['year']) ? $requestData['year'] : "";
        $month = !empty($requestData['month']) && isset($requestData['month']) ? $requestData['month'] : "";
        $pcode = !empty($requestData['prodcode']) && isset($requestData['prodcode']) ? $requestData['prodcode'] : "";

        $pdata = [];

        if (!empty($year) && !empty($month) && empty($pcode)) {
            $products = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                            'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                            'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                            'r.asofdate'
                    )->whereNotNull('r.product_id')
                    ->where(DB::raw('YEAR(r.asofdate)'), $year)
                    ->where(DB::raw('MONTH(r.asofdate)'), $month)
                    ->orderBy('r.asofdate')
                    ->get();
        } else if (empty($year) && empty($month) && !empty($pcode)) {
            $products = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                            'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                            'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                            'r.asofdate'
                    )->whereNotNull('r.product_id')
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.ac4', 'like', '%' . $pcode . '%')->orWhere('p.product_code', 'like', '%' . $pcode . '%')->orWhere('p.product_desc', 'like', '%' . $pcode . '%');
                    })
                    ->orderBy('r.asofdate')
                    ->get();
        } else if (!empty($year) && !empty($month) && !empty($pcode)) {
            $products = DB::table('runrate as r')
                    ->leftjoin("dbo.products as p", "p.prod_id", "=", "r.product_id")
                    ->select('r.product_id', 'r.ranking as Ranking', 'p.ac4 as ProductAC4', 'p.product_code as SigmaCode', 'p.product_desc as ProductDesc',
                            'r.monthly_total_sales as currentUsage', 'r.target_pack_sales as targetVolume', 'r.projected_percentage_over_target as CurrentRunRate',
                            'r.projected_sales as projectedVolume', DB::raw('r.projected_sales - r.target_pack_sales as projectedRunRate'), 'r.projected_percentage_over_target as projectedRunRateOverTarget',
                            'r.asofdate'
                    )->whereNotNull('r.product_id')
                    ->where(DB::raw('YEAR(r.asofdate)'), $year)
                    ->where(DB::raw('MONTH(r.asofdate)'), $month)
                    ->where(function ($query) use ($pcode) {
                        $query->where('p.ac4', 'like', '%' . $pcode . '%')->orWhere('p.product_code', 'like', '%' . $pcode . '%')->orWhere('p.product_desc', 'like', '%' . $pcode . '%');
                    })->orderBy('r.asofdate')
                    ->get();
        }


        $pdata["products"] = $products;

        if (count($products) > 0) {
            return $this->sendResponse($pdata, 'Runrate retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any Runrate data found']);
        }
    }

    /**
     * Adds/Updates Telesales price
     *
     * @param \Illuminate\Http\Request $request Request with contract prices
     *
     * @return json The Json response of result
     */
    public function addTelesalesPricing(Request $request)
    {

        try {

            $requestData = $request->all();
            //Gets request data
            $currentDateTime = Carbon::now();

            $inserted_by = $lastchanged_by = Auth::user()->id;

            $product = !empty($requestData['product']) && isset($requestData['product']) ? trim($requestData['product']) : "";
            $asofdate = !empty($requestData['asofdate']) && isset($requestData['asofdate']) ? trim($requestData['asofdate']) : NULL;
            $price = !empty($requestData['price']) && isset($requestData['price']) ? trim($requestData['price']) : NULL;
            $competitor = !empty($requestData['competitor']) && isset($requestData['competitor']) ? trim($requestData['competitor']) : NULL;
            $competitorupper = strtoupper($competitor);

            $competitorItem = DB::table('competitors')->where(DB::raw("UPPER(name)"), '=', $competitorupper)->select("competitor_id")->first();
            $competitorId = !empty($competitorItem->competitor_id) && isset($competitorItem->competitor_id) ? $competitorItem->competitor_id : '';

            if (empty($competitorId)) {
                $data = [
                    'name' => $competitorupper,
                    'inserted_by' => $inserted_by
                ];

                // Insert the data into the table and retrieve the ID of the newly inserted item
                $competitorId = DB::table('competitors')->insertGetId($data);
            }


            $telesalesP = DB::table('telesales_pricing')->where(["product_id" => $product, "asofdate" => $asofdate,
                        "competitor_id" => $competitorId])->pluck("telesales_id")->first();

            if (!empty($telesalesP)) {

                DB::table('telesales_pricing')
                        ->where(["telesales_id" => $telesalesP])
                        ->update(['price' => $price,
                            'updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

                return $this->sendResponse('Telesales Price Updated', 'Telesales price updated successfully.');
            } else {
                DB::table('telesales_pricing')->insert([
                    'product_id' => $product,
                    'competitor_id' => $competitorId,
                    'asofdate' => $asofdate,
                    'price' => $price,
                    'inserted_by' => $inserted_by
                ]);
                return $this->sendResponse('Telesales Price Added', 'Telesales price added successfully.');
            }
        } catch (\Exception $error) {
            return $this->sendErrorResponse([], 'Failed to add price ' . $error->getMessage(), 208);
        }
    }

    /**
     * Adds/Updates Competitor Offers
     *
     * @param \Illuminate\Http\Request $request Request with contract prices
     *
     * @return json The Json response of result
     */
    public function addCompetitorOffers(Request $request)
    {

        try {

            $requestData = $request->all();
            //Gets request data
            $currentDateTime = Carbon::now();

            $inserted_by = $lastchanged_by = Auth::user()->id;

            $product = !empty($requestData['product']) && isset($requestData['product']) ? trim($requestData['product']) : "";
            $sales_code = !empty($requestData['sales_code']) && isset($requestData['sales_code']) ? trim($requestData['sales_code']) : NULL;
            $trade_price = !empty($requestData['trade_price']) && isset($requestData['trade_price']) ? trim($requestData['trade_price']) : NULL;
            $price = !empty($requestData['price']) && isset($requestData['price']) ? trim($requestData['price']) : NULL;
            $stock = !empty($requestData['stock']) && isset($requestData['stock']) ? trim($requestData['stock']) : NULL;

            $offerI = DB::table('competitor_offers')->where(["product" => $product, "sales_code" => $sales_code,
                        "trade_price" => $trade_price, "price" => $price, "stock" => $stock])->pluck("offer_id")->first();

            if (!empty($offerI)) {

                DB::table('competitor_offers')
                        ->where(["offer_id" => $offerI])
                        ->update(['updated_at' => $currentDateTime, 'lastchanged_by' => $lastchanged_by]);

                return $this->sendResponse('Competitor Offer Updated', 'Competitor Offer updated successfully.');
            } else {
                DB::table('competitor_offers')->insert([
                    'product' => $product,
                    'sales_code' => $sales_code,
                    'trade_price' => $trade_price,
                    'price' => $price,
                    'stock' => $stock,
                    'asofdate' => date("Y-m-d"),
                    'inserted_by' => $inserted_by
                ]);
                return $this->sendResponse('Competitor Offer Added', 'Competitor Offer added successfully.');
            }
        } catch (\Exception $error) {
            return $this->sendErrorResponse([], 'Failed to add competitor offer ' . $error->getMessage(), 208);
        }
    }

    /**
     * Gets Competitor Offers
     *
     *
     * @return json The Json array of Competitor Offers
     */
    public function getCompetitorOffers()
    {

        $data = [];
        $offers = DB::table('competitor_offers')->select('product', 'sales_code',
                        DB::raw('CAST(trade_price AS DECIMAL(10,2)) AS trade_price'),
                        DB::raw('CAST(price AS DECIMAL(10,2)) AS price'),
                        'stock',
                        'asofdate')
                ->get();
        $data['offers'] = $offers;

        if (!empty($data['offers'])) {
            return $this->sendResponse($data, 'Competitor_offers retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any competitor_offers available.']);
        }
    }
    
    
    /**
     * Get latest product trends
     * 
     * @return json The Json array of Product Trends
     */
    public function getTopProductTrend()
    {
        $data = [];
       

        try {

            $trends = (new Product())->getLatesProductTrend();

            $data['trends'] = $trends->all();

           if (!empty($data)) {
                $message = 'Showing all latest trends';
            } else {
                $message = 'No any latest trend available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch latest trend', 'Failed : ' . $error->getMessage(), 208);
        }
    }
    
    
      /**
     * Gets all product trends details
     *
     * @return json The Json array of product trends data
     */
    public function getProductTrend($page,$sortcolumn, $sort)
    {
        $data = [];
        $page = (int) $page;
        $sort = (int) $sort;

        try {

            $usersTask = (new Product())->getTrends($page, $sortcolumn, $sort);

            $data['trends'] = $usersTask->all();

            $data['trends_count'] = (new Product())->getTrendsCount();
            $data['users'] = ProductBackground::getAllUsers();

          

            if (!empty($data)) {
                $message = 'Showing all trends';
            } else {
                $message = 'No any trend data available';
            }

            return $this->sendResponse($data, $message, 200);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to fetch trend', 'Failed : ' . $error->getMessage(), 208);
        }
    }

}
