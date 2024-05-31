<?php

use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController as ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController as LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ActivityLogController;




Route::get('/students', [StudentController::class, 'index']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/reset-password-email', [ForgotPasswordController::class, 'forgot']);
Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);

Route::middleware('auth:api')->group(function () {

    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/create-role', [RoleController::class, 'store']);
    Route::get('/view-role/{id}', [RoleController::class, 'show']);
    Route::put('/update-role/{id}', [RoleController::class, 'update']);
    Route::delete('delete-role/{id}', [RoleController::class, 'destroy']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/create-user', [AdminUserController::class, 'store']);
    Route::put('/update-user/{id}', [AdminUserController::class, 'update']);
    Route::delete('/delete-user/{id}', [AdminUserController::class, 'destroy']);
    Route::get('/view-profile', [UserController::class, 'profile']);
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword']);
    Route::put('/update-profile', [UserController::class, 'store']);

    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/create-tag', [TagController::class, 'store']);
    Route::get('/view-tag/{id}', [TagController::class, 'view']);
    Route::put('/update-tag/{id}', [TagController::class, 'update']);
    Route::delete('/delete-tag/{id}', [TagController::class, 'destroy']);

    //To List Live products 
    Route::get('/products/{page}/{sortcolumn}/{sort}', [ProductController::class, 'index']);
    //To list discontinued products
    Route::get('/discontinued-products/{page}/{sortcolumn}/{sort}', [ProductController::class, 'discontinued']);
    //To list New products
    Route::get('/new-products/{page}/{sortcolumn}/{sort}', [ProductController::class, 'new']);

    #Product Page APIs
    Route::get('/get-product-header/{productid}', [ProductController::class, 'getProductHeader']);
    Route::get('/get-product-tags-details/{productid}', [ProductController::class, 'getProductTagsDetails']);
    Route::get('/get-product-inventory-details/{productid}', [ProductController::class, 'getProductInventoryDetails']);
    Route::get('/get-product-ari-volume/{productid}', [ProductController::class, 'getProductARIVolume']);
    Route::get('/get-product-comments-details/{productid}', [ProductController::class, 'getProductCommentsDetails']);
    Route::get('/get-product-overview-data/{productid}', [ProductController::class, 'getOverviewData']);
    Route::get('/get-product-pricing-data/{productid}/{type}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getPricingData']);
    Route::post('/search-product-pricing-data/{productid}/{type}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchPricingData']);
   
    Route::get('/get-product-grn-data/{productid}', [ProductController::class, 'getGrnData']);
    Route::get('/get-price-capture-historical-details/{productid}/{sourceid}', [ProductController::class, 'getPriceCaptureHistoricalDetails']);
    Route::get('/get-product-pricing-usage-summary/{productid}', [ProductController::class, 'getPricingUsageSummary']);

#Supplier Page APIs
    Route::get('/supplier-details/{supplierid}/{page}/{sortcolumn}/{sort}', [SupplierController::class, 'showSupplierDetails']);
    Route::get('/supplier-po-details/{supplierid}', [SupplierController::class, 'showSupplierPoDetails']);
    Route::get('/supplier-grn-data', [SupplierController::class, 'getSupplierGRNData']);

    Route::get('/search-product/{keyword}', [ProductController::class, 'searchProduct']);
    Route::get('suppliers/{page}/{sortcolumn}/{sort}', [SupplierController::class, 'index']);
    Route::get('customers/{page}/{sortcolumn}/{sort}', [SupplierController::class, 'getCustomers']);
    Route::get('/supplier-grn-data/{supplierid}', [SupplierController::class, 'getSupplierGRNData']);
    Route::get('/get-supplier-product-pc-po-data/{supplierid}/{productid}', [SupplierController::class, 'getSupplierProductPcPoData']);
    Route::get('search-supplier/{keyword}', [SupplierController::class, 'searchSupplier']);

    Route::post('/update-negotiated-price', [ProductController::class, 'updateNegotiatedPrice']);
    Route::post('/attach-tag/{productid}', [ProductController::class, 'attachTag']);
    Route::put('/deactivate-tag/{productid}', [ProductController::class, 'deactivateTag']);

    Route::get('/tag-with-all-products/{tag_id}', [ProductController::class, 'tagWithAllProducts']);

    Route::post('/attach-comment/{productid}', [ProductController::class, 'addComment']);
    Route::post('/attach-comment-watchlist/{productid}', [ProductController::class, 'addCommentWatchlist']);
   // Route::post('/attach-comment-watchlist1/{productid}', [ProductController::class, 'addCommentWatchlist1']);
    Route::get('/get-pending-tasks', [CommentController::class, 'getPendingTasks']);
    Route::get('/get-product-pending-tasks/{prodid}', [CommentController::class, 'getProductPendingTasks']);
    //Tasks
    Route::get('/get-tasks-to-me/{page}/{sort}', [CommentController::class, 'getTasksToMe']);
    Route::get('/get-tasks-by-me/{page}/{sort}', [CommentController::class, 'getTasksByMe']);
    Route::post('/search-tasks-to-me', [CommentController::class, 'searchTasksToMe']);
    Route::post('/search-tasks-by-me', [CommentController::class, 'searchTasksByMe']);
    Route::get('/get-comments/{page}/{sortcolumn}/{sort}', [CommentController::class, 'getComments']);
    Route::get('/get-top-comments', [CommentController::class, 'getTopComments']);
    Route::get('/get-top-product-trends', [ProductController::class, 'getTopProductTrend']);
    Route::get('/get-product-trends/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getProductTrend']);
    Route::get('/user-pending-tasks', [CommentController::class, 'getUserPendingTasks']);
    Route::get('/get-loggedin-user-pending-tasks', [CommentController::class, 'getLoggedInUserPendingTasks']);
    Route::put('/update-task/{taskid}', [CommentController::class, 'update']);
    Route::put('/update-task-comment/{id}', [CommentController::class, 'updateTaskComment']);
    #Route::get('/get-comments/{productid}',[CommentController::class,'getComments']);
    Route::get('/get-all-tags', [ProductController::class, 'taglist']);
    #Route::put('/deactivate-comment', [ProductController::class, 'deactivatecomment']);
    Route::get('/get-product-codes/{parentcode}', [ProductController::class, 'productcodes']);
    Route::post('/add-product', [ProductController::class, 'addProduct']);
    Route::put('/edit-product/{prodid}', [ProductController::class, 'editProduct']);
    Route::get('/view-product/{prod_id}', [ProductController::class, 'viewProduct']);

    Route::get('/ails', [ProductController::class, 'supplierdetails']);

    # Gets Parent product list
    Route::get('/get-ac-products', [ProductController::class, 'geACProducts']);
    #Gets Supplier list
    Route::get('/get-suppliers', [ProductController::class, 'getSuppliers']);
    Route::get('/get-supplier-codes', [ProductController::class, 'getSupplierCodes']);
    #Purchanse Order
    Route::get('/pos', [PurchaseOrderController::class, 'index']);
    Route::get('/get-poitems/{poid}', [PurchaseOrderController::class, 'show']);
    Route::get('/allpos', [PurchaseOrderController::class, 'getPos']);
    Route::get('/poitems', [PurchaseOrderController::class, 'getPoItems']);
    Route::get('/all-downloaded-pos', [PurchaseOrderController::class, 'getDownloadedPos']);
    #List Pending Pos
    Route::get('/pending-pos', [PurchaseOrderController::class, 'pendingPos']);
    Route::post('/create-po-items', [PurchaseOrderController::class, 'store']);
    Route::post('/raise-po', [PurchaseOrderController::class, 'createPO']);
    Route::post('/check-po-exists', [PurchaseOrderController::class, 'checkPOExists']);
    Route::post('/download-pos', [PurchaseOrderController::class, 'DownloadPos']);

    Route::get('/recent-pos', [PurchaseOrderController::class, 'getRecentPos']);

    # Approve/Reject PO
    Route::put('/update-po/{id}', [PurchaseOrderController::class, 'update']);

    #Updates concerto number to the PO in PP
    Route::put('/add-concerto-reference/{id}', [PurchaseOrderController::class, 'addConcertOReference']);

    #Update the PO status as completed as per confirmation from user
    Route::put('/download-update-po-status/{id}', [PurchaseOrderController::class, 'updateStatus']);
    #Update the status as completed as per confirmation from user for multiple PO download
    Route::put('/download-update-allpo-status', [PurchaseOrderController::class, 'updateStatusOfAllPOs']);
    #Remove PO Item
    Route::put('/remove-poitem/{id}', [PurchaseOrderController::class, 'removePOItem']);
    #Add PO Item in existing PO
    Route::post('/add-po-items/{poId}', [PurchaseOrderController::class, 'addPOItems']);
    #Graph
    Route::get('/get-po-insight', [PurchaseOrderController::class, 'getPOInsight']);
    Route::get('logout', [LoginController::class, 'logout']);
    Route::get('/get-prod-grn/{productid}/{page}/{sortcolumn}/{sort}', [GRNController::class, 'getProdGRN']);
    Route::get('/get-supplier-grn/{supplierid}/{page}/{sortcolumn}/{sort}', [GRNController::class, 'getSupplierGRN']);
    Route::get('/get-historical-comments/{productid}/{page}/{sort}', [CommentController::class, 'getHistoricalComments']);
     Route::get('/get-historical-background/{productid}/{page}/{sort}', [CommentController::class, 'getHistoricalBackgrounds']);
    Route::post('/search-historical-comments', [CommentController::class, 'searchComments']);
    Route::post('/search-historical-background', [CommentController::class, 'searchBackground']);
    Route::get('/product-supplier-data/{productId}', [SupplierController::class, 'suppProductData']);

    Route::get('/get-contract-details/{productId}', [ProductController::class, 'productContractDetails']);
    Route::get('/get-kpi-details/{productId}', [ProductController::class, 'productKpiDetails']);
    Route::post('/change-kpi-details/{productId}', [ProductController::class, 'changeKpiDetails']);

    Route::get('/get-product-pricing-data2/{productid}', [ProductController::class, 'getPricingData2']);
    Route::get('/get-product-inventory-details2/{productid}', [ProductController::class, 'getProductInventoryDetails2']);

//    Route::get('/get-sig-usage-data/{productid}', [ProductController::class, 'getSigUsage']);
    Route::get('/get-sig-usage-data/{productid}', [ProductController::class, 'getSalesVolumeData']);
    # Add Price Indicator/Trend
    Route::post('/add-price-indicator/{productid}', [ProductController::class, 'addPriceIndicator']);

    # Supplier Page -> Products -> Stores the mapping for visited pricing record
    Route::post('/store-visited-pricingitem-mapping', [ProductController::class, 'storeVisitedPricingItemMapping']);

    #ARI Indicator

    Route::get('/get-ari-details/{productId}', [ProductController::class, 'getARIIndicator']);
    Route::get('/get-ari-info/{productId}', [ProductController::class, 'getARIInfo']);
    Route::get('/get-historical-ari-info/{productId}', [ProductController::class, 'getHistoricalARIInfo']);
    Route::post('/update-ari-indicator/{productId}', [ProductController::class, 'updateARIndicator']);
    #Gets Supplier list for ARI
    Route::get('/get-suppliers-for-ari/{productId}', [ProductController::class, 'getSuppliersForAri']);
    Route::put('/unassign-ari/{productid}', [ProductController::class, 'unassignAri']);
    ## Admin -Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/update-settings', [SettingsController::class, 'update']);

    # Gets Spot product pricng
    Route::get('/get-spot-product-pricing/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getSpotPricing']);
    Route::post('/search-spot-product-pricing/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchSpotPricing']);

    Route::get('/get-spot-product-pricing1/{page}/{sortcolumn}/{sort}/{pcode}', [ProductController::class, 'getSpotPricing1']);
    # Gets  supplier pricng
    Route::get('/get-supplier-pricing/{ac4}', [ProductController::class, 'getSupplierPricing']);

    Route::post('/update-supplier-pricing', [ProductController::class, 'updateSupplierPricing']);
    Route::post('/add-comment', [ProductController::class, 'addProductComment']);
    Route::put('/remove-tag/{pcid}', [ProductController::class, 'removeTag']);

    # Gets Spot product pricng
    Route::get('/get-competitor-contract-pricing/{ac4}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getCompetitorContractPricing']);
    Route::post('/search-competitor-contract-pricing/{ac4}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchCompetitorContractPricing']);
    Route::get('/get-supplier-historical-pricing/{ac4}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getHistoricalSupplierPricing']);
    Route::post('/search-supplier-historical-pricing/{ac4}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchHistoricalSupplierPricing']);
    # Gets Watch lists
    Route::get('/get-competitor-pricing/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getCompetitorPricing']);
    Route::get('/get-competitor-pricing-buyerset/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getCompetitorPricingForBuyerSet']);
    Route::get('/get-competitor-pricing-preset/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getCompetitorPricingForPreset']);
     Route::get('/get-competitor-pricing-preset-jayleshbhai/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getCompetitorPricingForPresetTwiceAWeek']);
    Route::get('/get-competitor-pricing-buyer-watchlist/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getCompetitorPricingForBuyerWatchlist']);
     Route::get('/get-undercost-lines/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getUndecostlines']);
    Route::get('/get-undercost-lines-watchlist/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getUndecostlinesWatchlist']);
     Route::post('/price-review', [ProductController::class, 'addUpdateCompetitorPricing']);

    Route::post('/download-price-review/{type}', [ProductController::class, 'downloadPriceReview']);

    Route::post('/search-competitor-pricing/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchCompetitorPricing']);

    Route::post('/download-competitor-pricing', [ProductController::class, 'downloadCompetitorPricing']);

    Route::get('/get-analytics-page-header/{prodcode}', [ProductController::class, 'getAnalyticsPageHeader']);

    Route::get('search-comment/{keyword}/', [ProductController::class, 'searchComment']);
    Route::get('get-predefined-comments', [ProductController::class, 'getPredefinedComment']);
    Route::get('search-predefined-comment/{keyword}', [ProductController::class, 'searchPredefinedComment']);
    Route::get('search-competitor/{keyword}', [ProductController::class, 'searchCompetitor']);

    Route::post('/add-to-watchlist', [ProductController::class, 'addToWatchList']);

    Route::post('/add-bulkproducts-watchlist', [ProductController::class, 'addBulkToWatchList']);

    Route::get('/get-watchlist/{type}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getWatchlist']);

    Route::post('/search-watchlist/{type}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchWatchlist']);
    Route::put('/remove-from-watchlist/{watchlistid}', [ProductController::class, 'removeFromWatchlist']);

    Route::get('/get-watchlist-catalog/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getWatchlistCatalog']);
    Route::get('/get-buyer-shortlisted/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getBuyerShortlisted']);
    Route::post('/search-watchlist-catalog/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchWatchlistCatalog']);
    Route::post('/notify-user', [ProductController::class, 'notifyReviewer']);

    Route::get('/get-pricing-list/{type}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getPricingList']);
    Route::post('/search-pricing-list/{type}/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchPricingList']);
    Route::post('/import-watchlist/{type}', [ProductController::class, 'importWatchList']);
    Route::post('/add-pricier-comment', [ProductController::class, 'addPricierComment']);
    
    Route::post('/update-contract-pricing', [ProductController::class, 'updateContractPricing']);
     
    Route::get('/download-product-catalog', [ProductController::class, 'downloadProductCatalog']);
      
    Route::get('/get-runrate/{page}/{sortcolumn}/{sort}', [ProductController::class, 'getRunrate']);
    Route::post('/download-runrate', [ProductController::class, 'downloadRunrate']);
    Route::post('/search-runrate/{page}/{sortcolumn}/{sort}', [ProductController::class, 'searchRunrate']);
    Route::post('/add-telesales-pricing', [ProductController::class, 'addTelesalesPricing']);
    Route::post('/add-competitor-offer', [ProductController::class, 'addCompetitorOffers']);
    Route::get('/get-competitor-offers', [ProductController::class, 'getCompetitorOffers']);
    
    Route::get('/get-product-latest-background', [ProductController::class, 'getProductLatestBackground']);
    Route::get('/get-activity-logs', [ActivityLogController::class, 'index']);
    Route::put('/activity-logs/{logId}/mark-as-read', [ActivityLogController::class, 'markAsRead']);
    Route::post('/search-product-comment/{page}/{sortcolumn}/{sort}', [CommentController::class, 'filterComments']);
});

