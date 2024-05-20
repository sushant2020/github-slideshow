<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ImportProductMaster extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';
	

    protected $description = "This command imports the newly uploaded products from the staging.products to the dbo.products.The products are transffered to staging.products using the data factory";

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
		
		$getProducts = DB::select(DB::raw("select * from staging.products p where p.created_at > (SELECT MAX(created_at) from products)"));
		
		$insertProductsItems = json_decode(json_encode($getProducts), true);
		 $cnt = count($insertProductsItems);
		if($cnt > 0) {

		foreach ($insertProductsItems as $key => $item) {
			unset($item['prod_id']);
				Product::insert($item);
            }
		}
		
		$time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
     
		if($cnt > 0) {
        $output_message = "Inserted $cnt records from staging.products successfully at " . now();
		} else {
			$output_message = "No any records available for insertion.Checked at " . now();
		}

        echo PHP_EOL . $output_message . PHP_EOL;
       	echo PHP_EOL . "Process Completed at $now " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
		mail('sushant@webdezign.co.uk','Product Data import for Product portal dbo.products',$output_message);
	}
	


}
