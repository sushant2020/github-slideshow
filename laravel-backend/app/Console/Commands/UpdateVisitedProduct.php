<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * This class file is created to expire the po's whcih are older than 30 days.
 */
class UpdateVisitedProduct extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:user_visited_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command removes the visited products from highlight";

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
     * On Supplier page, products section when user clicks on any Aggragate code it redirects to Product page in supplier mode. As soon as user comes back to supplier page, the visited product row shows in light grey background.
	 * This commands check the start date of visited product and comares it with current date. If the difference is greater than the configured days, it removes/expires the visted highlight.
	  * The configured days[say 21 days here]
     *
     * @return void
     */
    public function handle()
    {
		$time_start = microtime(true);
		 echo PHP_EOL . "Process Started ..... " . PHP_EOL;
		
		$now = date('Y-m-d H:i:s');
		$userId = User::where("email", "sushant@webdezign.co.uk")->pluck("id")->first();
		$daysBefore = Carbon::now()->subDays(21);


		$updateCnt = DB::table('user_visited_products')			
			->where('created_at', '<', $daysBefore)
			->whereNull('expired_at')
			->count();
		
    	if($updateCnt > 0) {
			DB::table('user_visited_products')			
			->where('created_at', '<', $daysBefore)
				->whereNull('expired_at')
			->update(["expired_at" => $now, "updated_at" => $now, "lastchanged_by" => $userId]);
			 $output_message = "Updated $updateCnt records of visited products successfully at " . now();
		} else {
			 $output_message = "No any records available to update. Checked at " . now();
		}
			
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
       

        echo PHP_EOL . $output_message . PHP_EOL;
        echo PHP_EOL . "Process Completed ..... " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
	}

}
