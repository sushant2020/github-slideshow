<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ClDesProduct;
use App\Models\ProductCombo;

class ProductComboImport extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:products_combo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands maps data from pricing table-cl_des_products and Data Ware House product data and stores in final table products';

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
		ini_set('max_execution_time', '800');
        $time_start = microtime(true);

   

         mail('sushant@webdezign.co.uk','Product Combo Importing','Started');

        (new ProductCombo())->updateDescription();
        
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $output_message = "Product data imported successfully at " . now();

        echo PHP_EOL . $output_message . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000) / 60) . ' minutes' . PHP_EOL;
		mail('sushant@webdezign.co.uk','Product Combo Importing','Ended');
    }

}
