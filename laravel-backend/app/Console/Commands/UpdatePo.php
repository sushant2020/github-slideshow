<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Components\Helper;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;

/**
 * This class file is created to expire the po's whcih are older than 30 days.
 */
class UpdatePo extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:po';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command expires the non approved po's which are older than 30 days.";

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
		
		$holidays = [];
		$month_30 = Helper::GetWorkingHisDate($holidays,-30);
		$expired = PurchaseOrder::PO_EXPIRED;
		$month_30 = $month_30 . " 00:00:00";
		$month_30 = date($month_30);
		$status = [PurchaseOrder::PO_IN_PROGRESS, PurchaseOrder::PO_APPROVAL_PENDING];
		
		$now = date('Y-m-d H:i:s');
		$user = 55;
		
		$updateCnt = DB::table('purchase_orders')			
			->where('purchase_orders.created_at', '<', $month_30)
			->whereIn('purchase_orders.status', $status)
			->count();
		
    	$po = DB::table('purchase_orders')			
			->where('purchase_orders.created_at', '<', $month_30)
			->whereIn('purchase_orders.status', $status)
			->update(["purchase_orders.status" => $expired, "updated_at" => $now, "lastchanged_by" => $user]);
			
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $output_message = "Updated $updateCnt records of PO data successfully at " . now();

        echo PHP_EOL . $output_message . PHP_EOL;
        echo PHP_EOL . "Process Completed ..... " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
	}

}
