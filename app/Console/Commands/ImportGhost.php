<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\Ghost;
use Illuminate\Support\Facades\Auth;

/**
 * This class file is created to expire the po's whcih are older than 30 days.
 */
class ImportGhost extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ghost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command imports the newly uploaded ghost from the staging.dw_ghost to the dbo.ghost.";

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
		
		
		$newUsageData = DB::select(DB::raw("select Company as company, Spot_Code as product_code, First_Ghost as ssf_alt_prod_code_1, Second_Ghost as ssf_alt_prod_code_2, Third_Ghost as ssf_alt_prod_code_3, Fourth_Ghost as ssf_alt_prod_code_4, Fifth_Ghost as ssf_alt_prod_code_5, Ghost_Agg_Code as ghost_agg_code, Inserted_DateTime as created_at, Updated_DateTime as updated_at, '1' as inserted_by, '1' as lastchanged_by,'1' as lastchanged_by, '1' as is_latest from staging.dw_Ghost where Inserted_DateTime > (SELECT MAX(created_at) from ghost)"));
		
		$insertUsageDataItems = json_decode(json_encode($newUsageData), true);

		$cnt = count($insertUsageDataItems);
	
		if($cnt > 0) {
			Ghost::where('is_latest',  1)
	->update([
		'is_latest' => 0
	]);
			
		foreach (array_chunk($insertUsageDataItems, (2100 / 22) - 2) as $chunk) {
				Ghost::insert($chunk);
            }
		}
		
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
		
		if($cnt > 0) {
        $output_message = "Inserted $cnt records from staging.dw_ghost successfully at " . now();
		} else {
			$output_message = "No any records available for insertion. Checked at " . now();
		}

        echo PHP_EOL . $output_message . PHP_EOL;
       	echo PHP_EOL . "Process Completed at $now " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
		mail('sushant@webdezign.co.uk','Ghost Data import',$output_message);
	}

}
