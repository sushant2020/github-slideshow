<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use App\components\Helper;
use App\ImportLogger;
use App\DwProduct;

/**
 * Product master from Pricing
 */
class ClDesProduct extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cl_des_products';
	
	protected $primaryKey = 'product_id';
	
    protected $fillable = [
        'product_code',
        'sm_analysis_code1',
        'sm_analysis_code2',
        'sm_analysis_code3',
        'ac4',
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
        'final_price',
        'dq_status',
    ];
    
    /**
     * Imports the product data into cl_des_products table from file provided By Sigma/Krishna.
     * This file has clean description for products
     * 
     * @return integer Existing Product Count
     */
    public static function importProductData($importLoggerId, $file, $importData_arr, $userId)
    {

        $output = $rowMsgs = $errorLines = $finalData = [];
        $count = $existingCnt = 0;
        $output_message = $error = "";

        $import_method = ImportLogger::FILE_IMPORT;
		
        foreach ($importData_arr as $key => $importData) {
            $key = $key + 1;
            $productCode = !empty($importData["a_prod_code"]) && isset($importData["a_prod_code"]) ? trim($importData["a_prod_code"]) : "";
            $parentProductCode = !empty($importData["sm_analysis_code_4"]) && isset($importData["sm_analysis_code_4"]) ? trim($importData["sm_analysis_code_4"]) : "";
            $vmpp = !empty($importData["vmpp"]) && isset($importData["vmpp"]) ? trim($importData["vmpp"]) : "";
            $ampp = !empty($importData["ampp"]) && isset($importData["ampp"]) ? trim($importData["ampp"]) : "";
            $ske_analysis_26 = !empty($importData["ske_analysis_26"]) && isset($importData["ske_analysis_26"]) ? trim($importData["ske_analysis_26"]) : "";

            $clean_description = "";
            $data = [];
            $existingProduct = ClDesProduct::where(["product_code" => $productCode, "sm_analysis_code4" => $parentProductCode])->select('product_id', 'dt_description')->first();
            $vmpp_description = !empty($importData["vmpp_clean_description"]) && isset($importData["vmpp_clean_description"]) && $importData["vmpp_clean_description"] != '#N/A'  && !preg_match("/N\/A/", $importData["vmpp_clean_description"]) ? trim($importData["vmpp_clean_description"]) : "";
            $ampp_description = !empty($importData["ampp_clean_description2"]) && isset($importData["ampp_clean_description2"]) && $importData["ampp_clean_description2"] != '#N/A' && !preg_match("/N\/A/", $importData["ampp_clean_description2"]) ? trim($importData["ampp_clean_description2"]) : "";
		
            $dt_description = !empty($existingProduct["dt_description"]) && isset($existingProduct["dt_description"]) ? trim($existingProduct["dt_description"]) : "";

                //Where an AMPP code/description is present, this will be what is displayed. If there is not, the VMPP code/description should be used
                //Falling that, the sigma description[dt description] can be used
            $clean_description = !empty($ampp_description) ? $ampp_description : (!empty($vmpp_description) ? $vmpp_description : $dt_description);
                //Ignore empty rows
            if (empty($parentProductCode) && empty($productCode) && empty($vmpp) && empty($ampp) && empty($ske_analysis_26) && empty($vmpp_description) && empty($ampp_description)) {
                continue;
            }
            //validations
            if (empty($parentProductCode) && empty($productCode)) {
                $sNameMessage = "Either Aggregate Code or Product Code should be present";
				$errorLines[] = $key + 1;
                $dArr = [
                    'rownum' => $key + 1,
                    'colname' => "Aggregate Code - Product Code",
                    'msg' => $sNameMessage
                ];
                $rowMsgs[$key + 1][] = $dArr;
            }
            if (!empty($parentProductCode) || !empty($productCode)) {
				
				 if (!empty($parentProductCode) && empty($productCode)) {
					 $dwProductItemId = DwProduct::where(["Product_AC_4" => $parentProductCode])->pluck("Product_Id")->first();
					
					 if(empty($dwProductItemId)) {
						 
					 $sNameMessage = "Aggregate Code is not present in Datawarehouse";
					   $errorLines[] = $key + 1;
                $dArr = [
                    'rownum' => $key + 1,
                    'colname' => "Aggregate Code",
                    'msg' => $sNameMessage
                ];
                $rowMsgs[$key + 1][] = $dArr;
						
					 }
					
				 }
				if (!empty($productCode) && empty($parentProductCode) ) {
					
					$dwProductItemId = DwProduct::where(["Product_Code" => $productCode])->pluck("Product_Id")->first();
					 if(empty($dwProductItemId)) {
					 $sNameMessage = "Product Code is not present in Datawarehouse";
					   $errorLines[] = $key + 1;
                $dArr = [
                    'rownum' => $key + 1,
                    'colname' => "Product Code",
                    'msg' => $sNameMessage
                ];
                $rowMsgs[$key + 1][] = $dArr;
					 }
				 }
				 if (!empty($parentProductCode) && !empty($productCode)) {
					 $dwProductItemId = DwProduct::where(["Product_AC_4" => $parentProductCode, "Product_Code" => $productCode])->pluck("Product_Id")->first();
					 if(empty($dwProductItemId)) {
					 $sNameMessage = "Aggregate Code & Product Code are not present in Datawarehouse";
						 $errorLines[] = $key + 1;
                $dArr = [
                    'rownum' => $key + 1,
                    'colname' => "Aggregate Code - Product Code",
                    'msg' => $sNameMessage
                ];
                $rowMsgs[$key + 1][] = $dArr;
					 }
				 }
				
                if (!empty($existingProduct)) {
                    $existingCnt++;
					  if (!in_array($key + 1, $errorLines)) {
                    $data = array(
                        //  "sm_description" => trim($importData["sm_description"]),
                        // "sm_description2" => trim($importData["sm_description_2"]),
                        //  "sm_description3" => !empty($importData["sm_description_3"]) && isset($importData["sm_description_3"]) ? trim($importData["sm_description_3"]) : "",
                                        "sm_analysis_code1" => isset($importData["sm_analysis_code_1"]) ? trim($importData["sm_analysis_code_1"]) : "",
                                        "sm_analysis_code2" => !empty($importData["sm_analysis_code_2"]) && isset($importData["sm_analysis_code_2"]) ? trim($importData["sm_analysis_code_2"]) : "",
                                        "sm_analysis_code3" => !empty($importData["sm_analysis_code_3"]) && isset($importData["sm_analysis_code_3"]) ? trim($importData["sm_analysis_code_3"]) : "",
                                        "sm_analysis_code5" => !empty($importData["sm_analysis_code_5"]) && isset($importData["sm_analysis_code_5"]) ? trim($importData["sm_analysis_code_5"]) : "",
                                        "sm_analysis_code6" => !empty($importData["sm_analysis_code_6"]) && isset($importData["sm_analysis_code_6"]) ? trim($importData["sm_analysis_code_6"]) : "",
                                        "product_group" => !empty($importData["a_prod_group"]) && isset($importData["a_prod_group"]) ? trim($importData["a_prod_group"]) : "",
                                        "product_group2" => !empty($importData["a_prod_group2"]) && isset($importData["a_prod_group2"]) ? trim($importData["a_prod_group2"]) : "",
                                        "sm_bin_loc" => !empty($importData["sm_bin_loc"]) && isset($importData["sm_bin_loc"]) ? trim($importData["sm_bin_loc"]) : "",
                        "vmpp" => $vmpp,
                        "ampp" => $ampp,
                        "ske_analysis26" => $ske_analysis_26,
                        "vmpp_description" => $vmpp_description,
                        "ampp_description" => $ampp_description,
                        "clean_description" => $clean_description,
                                         "vmpp_char_count" => !empty($importData["vmpp_charc_count"]) && isset($importData["vmpp_charc_count"]) ? trim($importData["vmpp_charc_count"]) : "",
                                        "dt_description" => !empty($importData["dt_desc"]) && isset($importData["dt_desc"]) ? trim($importData["dt_desc"]) : "",
                                         "dt_pack" => !empty($importData["dt_pack"]) && isset($importData["dt_pack"]) ? (int) $importData["dt_pack"] : "",
                                         "dt_type" => !empty($importData["dt_type"]) && isset($importData["dt_type"]) ? trim($importData["dt_type"]) : "",
                                         "dt_price" => !empty($importData["dt_price"]) && isset($importData["dt_price"]) && is_numeric($importData["dt_price"]) ? trim($importData["dt_price"]) : "",
                                         "final_price" => !empty($importData["final_price"]) && isset($importData["final_price"]) && is_numeric($importData["final_price"]) ? trim($importData["final_price"]) : "",
//                                         "dq_status" => !empty($importData["a_prod_code"]) && isset($importData["a_prod_code"]) && !empty($importData["sm_analysis_code_4"]) && isset($importData["sm_analysis_code_4"]) && !empty($importData["dt_desc"]) && isset($importData["dt_desc"]) && !empty($importData["dt_pack"]) && isset($importData["dt_pack"]) &&
//                                         !empty($importData["dt_type"]) && isset($importData["dt_type"]) ? 'complete' : 'incomplete',
                        "lastchanged_by" => $userId,
                        "updated_at" => now());

                    DB::table('cl_des_products')
                            ->where(["product_code" => $productCode, "sm_analysis_code4" => $parentProductCode])
                            ->update($data);
				}
                } else {
                    $count++;
					  if (!in_array($key + 1, $errorLines)) {
                    $data = array(
                        "product_code" => $productCode,
//                                        "sm_description" => trim($importData["sm_description"]),
//                                        "sm_description2" => trim($importData["sm_description_2"]),
//                                        "sm_description3" => !empty($importData["sm_description_3"]) && isset($importData["sm_description_3"]) ? trim($importData["sm_description_3"]) : "",
                                        "sm_analysis_code1" => isset($importData["sm_analysis_code_1"]) ? $importData["sm_analysis_code_1"] : "",
                                        "sm_analysis_code2" => !empty($importData["sm_analysis_code_2"]) && isset($importData["sm_analysis_code_2"]) ? trim($importData["sm_analysis_code_2"]) : "",
                                        "sm_analysis_code3" => !empty($importData["sm_analysis_code_3"]) && isset($importData["sm_analysis_code_3"]) ? trim($importData["sm_analysis_code_3"]) : "",
                        "sm_analysis_code4" => $parentProductCode,
                                       "sm_analysis_code5" => !empty($importData["sm_analysis_code_5"]) && isset($importData["sm_analysis_code_5"]) ? trim($importData["sm_analysis_code_5"]) : "",
                                        "sm_analysis_code6" => !empty($importData["sm_analysis_code_6"]) && isset($importData["sm_analysis_code_6"]) ? trim($importData["sm_analysis_code_6"]) : "",
                                        "product_group" => !empty($importData["a_prod_group"]) && isset($importData["a_prod_group"]) ? trim($importData["a_prod_group"]) : "",
                                       "product_group2" => !empty($importData["a_prod_group2"]) && isset($importData["a_prod_group2"]) ? trim($importData["a_prod_group2"]) : "",
                                        "sm_bin_loc" => !empty($importData["sm_bin_loc"]) && isset($importData["sm_bin_loc"]) ? trim($importData["sm_bin_loc"]) : "",
                        "vmpp" => $vmpp,
                        "ampp" => $ampp,
                        "ske_analysis26" => $ske_analysis_26,
                        "vmpp_description" => $vmpp_description,
                        "ampp_description" => $ampp_description,
                        "clean_description" => $clean_description,
                                        "vmpp_char_count" => !empty($importData["vmpp_charc_count"]) && isset($importData["vmpp_charc_count"]) ? trim($importData["vmpp_charc_count"]) : "",
                                        "dt_description" => !empty($importData["dt_desc"]) && isset($importData["dt_desc"]) ? trim($importData["dt_desc"]) : "",
                                       "dt_pack" => !empty($importData["dt_pack"]) && isset($importData["dt_pack"]) ? (int) $importData["dt_pack"] : "",
                                        "dt_type" => !empty($importData["dt_type"]) && isset($importData["dt_type"]) ? $importData["dt_type"] : "",
                                         "dt_price" => !empty($importData["dt_price"]) && isset($importData["dt_price"]) && is_numeric($importData["dt_price"]) ? trim($importData["dt_price"]) : "",
                                        "final_price" => !empty($importData["final_price"]) && isset($importData["final_price"]) && is_numeric($importData["final_price"]) ? trim($importData["final_price"]) : "",
                                          //"dq_status" => !empty($importData["a_prod_code"]) && isset($importData["a_prod_code"]) && !empty($importData["sm_analysis_code_4"]) && isset($importData["sm_analysis_code_4"]) && !empty($importData["dt_desc"]) && isset($importData["dt_desc"]) && !empty($importData["dt_pack"]) && isset($importData["dt_pack"]) &&
                                         // !empty($importData["dt_type"]) && isset($importData["dt_type"]) ? 'complete' : 'incomplete',
                        "created_at" => now(),
                        "inserted_by" => $userId,
                        "lastchanged_by" => $userId,
                        "updated_at" => now());
                    ClDesProduct::insert($data);
				}
                }
            }
        }

        $fArray = Helper::flatten($rowMsgs);

        $rowErrorMsg = "<table class='table table-striped'>
                        <thead><tr><th scope='col'>Row Number</th>
                            <th scope='col'>Column Name</th>
                            <th scope='col'>Message</th>
                        </tr>
                      </thead>
                      <tbody>";
        foreach ($fArray as $rowMsg) {
            $rowErrorMsg .= "<tr>
                                    <td>" . $rowMsg['rownum'] . "</td>
                                    <td>" . $rowMsg['colname'] . "</td>
                                    <td>" . $rowMsg['msg'] . "</td>
                                  </tr>";
        }
        $rowErrorMsg .= "</tbody></table>";
        $rowErrorMsg = trim($rowErrorMsg, '\'"');
        

        if (empty($rowMsg)) {

            if ($count == $existingCnt) {
                $output_message = 'All the records of a file have already been imported by another user.';
            }
            if ($count != $existingCnt && $existingCnt > 0) {
                $output_message = "File Imported with $existingCnt duplicate records";
            }

            if ($count != 0 && $existingCnt == 0) {
                $output_message = "Product master data updated successfully at.";
            }
        } else {

            if (!empty($rowErrorMsg) && !empty($rowMsg)) {


                if ($count == $existingCnt && ($count > 0 && $existingCnt > 0 )) {
                    $output_message = "All the records of a file have already been imported by another user and  there are some errors for few records. Please check <a target = '_blank' href='" . config('app.url') . "/viewlog/" . $importLoggerId . "'>
                    Check Errors</a>";
                } else {

                    if ($existingCnt > 0) {
                        $output_message = "File Imported with $existingCnt duplicate records and  there are some errors for few records. Please check <a target = '_blank' href='" . config('app.url') . "/viewlog/" . $importLoggerId . "'>
                    Check Errors</a>";
                    } else {
                        $output_message = "File Imported but there are some errors for few record. Please check <a target = '_blank'  href='" . config('app.url') . "/viewlog/" . $importLoggerId . "'>
                    Check Errors
                   </a>";
                    }
                }
                $error = $rowErrorMsg;
            }
        }


        ImportLogger::updateImportLog($importLoggerId, $file, $userId, $import_method, $output_message, $error);

        return $output_message;
    }

}
