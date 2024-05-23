<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\PricingUsageData;
use Illuminate\Support\Facades\Auth;

/**
 * This class file is created to expire the po's whcih are older than 30 days.
 */
class ImportUsageData extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:usage_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command imports the newly uploaded usage_data from the staging.usage_data to the dbo.usage_data.";

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
		
		$newUsageData = DB::select(DB::raw("select source_id, supplier_id, internal_supplier_id, parent_product_code, volume, volume_from_date, volume_untill_date, comments, created_at, updated_at, inserted_by, lastchanged_by, logger_id, original_volume_from_date, original_volume_untill_date, import_type from staging.usage_data us where us.created_at > (SELECT MAX(created_at) from usage_data)"));
		
		$insertUsageDataItems = json_decode(json_encode($newUsageData), true);
		
		$cnt = count($insertUsageDataItems);
	
		if($cnt > 0) {
		foreach (array_chunk($insertUsageDataItems, (2100 / 22) - 2) as $chunk) {
				PricingUsageData::insert($chunk);
            }
		}
		
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
		
		if($cnt > 0) {
        $output_message = "Inserted $cnt records from staging.usage_data successfully at " . now();
		} else {
			$output_message = "No any records available for insertion. Checked at " . now();
		}

        echo PHP_EOL . $output_message . PHP_EOL;
       	echo PHP_EOL . "Process Completed at $now " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
		mail('sushant@webdezign.co.uk','Usage Data import',$output_message);
	}

}
