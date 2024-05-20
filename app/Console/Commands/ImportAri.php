<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\Ari;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

/**
 * This class file is created to expire the po's whcih are older than 30 days.
 */
class ImportAri extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ari';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command imports the newly uploaded ari from the staging.dw_Ari to the dbo.ari.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     *
     * @return mixed
     */
   	public function handle()
    {
		$time_start = microtime(true);
		
		$now = date('Y-m-d H:i:s');
		echo PHP_EOL . "Process Started at $now " . PHP_EOL;
		$cnt = 0;
		
		$newAriData = DB::select(DB::raw("select ARI, Agg_Code, ARI, Supplier_Code, Inserted_DateTime, Updated_DateTime from staging.dw_Ari where Inserted_DateTime > (SELECT MAX(created_at) from ari_indicator)"));
			//$newAriData = DB::select(DB::raw("select ARI, Agg_Code, ARI, Supplier_Code, Inserted_DateTime, Updated_DateTime from staging.dw_Ari"));
		
		$insertAriItems = json_decode(json_encode($newAriData), true);
		$cnt = count($insertAriItems);
	
		$newElements = [];
		if($cnt > 0) {
			
			Ari::where('is_ari_supplier',  1)->where('is_manual',  0)
	->update([
		'is_ari_supplier' => 0
	]);
			
		foreach ($insertAriItems as $item) {
			$agg_Code = !empty($item["Agg_Code"]) && isset($item["Agg_Code"]) ? trim($item["Agg_Code"]) : '';
			$supplierCode = !empty($item["Supplier_Code"]) && isset($item["Supplier_Code"]) ? trim($item["Supplier_Code"]) : '';
			$created_at = !empty($item["Inserted_DateTime"]) && isset($item["Inserted_DateTime"]) ? trim($item["Inserted_DateTime"]) : '';
			$updated_at = !empty($item["Updated_DateTime"]) && isset($item["Updated_DateTime"]) ? trim($item["Updated_DateTime"]) : '';
			
			
			$prodId = Product::where("ac4", $agg_Code)->where("is_parent", 1)->pluck("prod_id")->first();
			$suppId = Supplier::where("type", 1)->where("code", $supplierCode)->where("company_id", 207791)->pluck("id")->first();
			
			if(!empty($item["ARI"]) && isset($item["ARI"]) && trim($item["ARI"]) == 'y') {
				$isAri = 1;
			} else {
				$isAri = 0;
			}
			
			if(!empty($prodId) && !empty($suppId)) {
				$newElements[] = ["product_id" => $prodId,"supplier_id" => $suppId,"is_ari_supplier" => $isAri, 
								"created_at" => $created_at, "updated_at" => $updated_at];
			}
            }
			
			foreach (array_chunk($newElements, (2100 / 9) - 2) as $chunk) {
				Ari::insert($chunk);
            }
		}
		
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
		
		if($cnt > 0) {
        $output_message = "Inserted $cnt records from staging.dw_Ari successfully at " . now();
		} else {
			$output_message = "No any records available for insertion. Checked at " . now();
		}

        echo PHP_EOL . $output_message . PHP_EOL;
       	echo PHP_EOL . "Process Completed at $now " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
		mail('sushant@webdezign.co.uk','ARI Data import',$output_message);
	}

}
