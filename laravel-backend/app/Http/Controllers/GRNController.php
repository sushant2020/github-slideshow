<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\Product;
use App\Models\Supplier;

class GRNController extends BaseController
{
   
	/**
     * Gets GRN Details of Product
     *
     * @param int $productid The Product ID
     *
     * @return json The Json array of GRN data
     */
    public function getProdGRN($productid, $page, $sortcolumn, $sort)
    {
        $productid = (int) $productid;
        $data = [];
		$data['header'] = Product::getProductHeader($productid);
		$gData = GRN::getProductGRNData($productid, $page, $sortcolumn, $sort);
		$grnItems = !empty($gData["grn"]) && isset($gData["grn"]) ? $gData["grn"] : '';
		$rowCount = !empty($gData["rowCnt"]) && isset($gData["rowCnt"]) ? $gData["rowCnt"] : '';
	
		$data['grn'] = $grnItems;
		$data['rowCount'] = $rowCount;
       

        if (!empty($data)) {
            return $this->sendResponse($data, 'GRN details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any GRN details found']);
        }
    }
	
	
	    /**
     * Gets 
     * Latest GRN Details of Supplier
     *
     * @param int $suppplierId The Supplier ID
     *
     * @return json The Json array of GRN data
     */
    public function getSupplierGRN($suppplierId, $page, $sortcolumn, $sort)
    {
        $suppplierId = (int) $suppplierId;
        $data = [];
 		$data['header'] = Supplier::getSupplierHead($suppplierId);
        $gData = GRN::getSupplierGRNData($suppplierId, $page, $sortcolumn, $sort);
		
		$grnItems = !empty($gData["grn"]) && isset($gData["grn"]) ? $gData["grn"] : '';
		$rowCount = !empty($gData["rowCnt"]) && isset($gData["rowCnt"]) ? $gData["rowCnt"] : '';
	
		$data['grn'] = $grnItems;
		$data['rowCount'] = $rowCount;
       

        if (!empty($data)) {
            return $this->sendResponse($data, 'GRN details retrieved successfully.');
        } else {
            return $this->sendError('NOT FOUND', ['error' => 'No any GRN details found']);
        }
    }
	

}
