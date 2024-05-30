<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\PricingSupplierPriceData;
use Illuminate\Support\Facades\Auth;

/**
 * This class file is created to expire the po's whcih are older than 30 days.
 */
class ImportPricingData extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:pricing_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command imports the newly uploaded pricing from the staging.pricing_data to the dbo.pricing_data.";

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
		
		$getNewPricing = DB::select(DB::raw("select source_id, supplier_id, internal_supplier_id, parent_product_code, product_code, price, price_from_date, price_untill_date, forecast, comments, created_at, updated_at, inserted_by, lastchanged_by, logger_id, is_shortdated, original_price_from_date, original_price_untill_date, import_type, negotiated_price, price_type  from staging.pricing_data pd2 where pd2.created_at > (SELECT MAX(created_at) from pricing_data)"));
		
		$insertPricingItems = json_decode(json_encode($getNewPricing), true);
		
		$cnt = count($insertPricingItems);
		if($cnt > 0) {
		foreach (array_chunk($insertPricingItems, (2100 / 22) - 2) as $chunk) {
				PricingSupplierPriceData::insert($chunk);
            }
		}
		
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        //$output_message = "Inserted $updateCnt records from staging.pricing_data successfully at " . now();
		if($cnt > 0) {
        $output_message = "Inserted $cnt records from staging.pricing_data successfully at " . now();
		} else {
			$output_message = "No any records available for insertion.Checked at " . now();
		}

        echo PHP_EOL . $output_message . PHP_EOL;
       	echo PHP_EOL . "Process Completed at $now " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
		mail('sushant@webdezign.co.uk','Pricing Data import',$output_message);
	}

}
