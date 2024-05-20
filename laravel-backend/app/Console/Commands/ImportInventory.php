<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

/**
 * This class file is created to write a function to import Inventory data from staging to main table of product portal
 */
class ImportInventory extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:inventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands fetches latest records from dw_Inventory data and stores in main table "Inventory" of product portal';

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
        ini_set('memory_limit', -1);

        $time_start = microtime(true);
        $insertCnt = $ltstInventoryStagingCnt = $updateCnt = 0;

        echo PHP_EOL . "Process Started ..... " . PHP_EOL;
        //Get the latest date of inserted record from Inventory staging table
        $latestStagingDateObj = DB::table('staging.dw_Inventory')
                ->select(DB::raw('max(Inserted_DateTime) as maxInDate'))
                ->first();
        $latestStagingDate = !empty($latestStagingDateObj) ? trim($latestStagingDateObj->maxInDate) : "";

        //Get the latest date of inserted record from Inventory main table
        $latestDateObj = DB::table('dbo.Inventory')
                ->select(DB::raw('max(Inserted_DateTime) as maxInDate'))
                ->first();
        $latestDate = !empty($latestDateObj) ? trim($latestDateObj->maxInDate) : "";

        if ($latestStagingDate != $latestDate) {
            //Get the latest inserted records from staging Inventory table
            $stagingInventory = DB::select(DB::raw("select Company_Id,Product_Id,Depot_Id,LG_Date,LS_Date,Physical_Stock,Allocation_Stock,Allocation_After,On_Order,Backorder,LG_Number,LPP_Cost,Avg_Cost,True_Cost,Min_Stock,Std_Cost,Max_Stock,Average_usage,Pick_Bin,Average_usage_UOM,Average_usage_Period,Inserted_DateTime,Updated_DateTime,Inserted_By,Updated_By from staging.dw_Inventory where Inserted_DateTime = (select max(Inserted_DateTime) from staging.dw_Inventory)"));

            $stagingInventoryData = json_decode(json_encode($stagingInventory), true);

            //Get the total count of latest inserted from staging Inventory table
            $lstInventoryStagingCnt = count($stagingInventoryData);

            $insertCnt = $lstInventoryStagingCnt;
            foreach (array_chunk($stagingInventoryData, (2100 / 44) - 2) as $chunk) {
                Inventory::insert($chunk);
            }
            echo PHP_EOL . "inserted fresh  $insertCnt records" . PHP_EOL;
        }


        //Update existing record
        $updateCnt = 0;
        //Get the latest updated records from staging Inventory table
        $updatedInventory = DB::select(DB::raw("select Company_Id,Product_Id,Depot_Id,LG_Date,LS_Date,Physical_Stock,Allocation_Stock,Allocation_After,On_Order,Backorder,LG_Number,LPP_Cost,Avg_Cost,True_Cost,Min_Stock,Std_Cost,Max_Stock,Average_usage,Pick_Bin,Average_usage_UOM,Average_usage_Period,Inserted_DateTime,Updated_DateTime,Inserted_By,Updated_By from staging.dw_Inventory where Updated_DateTime = (select max(Updated_DateTime) from staging.dw_Inventory)"));

        $updatedInventoryItems = json_decode(json_encode($updatedInventory), true);

        foreach ($updatedInventoryItems as $updatedInventoryItem) {
            $Company_Id = !empty($updatedInventoryItem) && isset($updatedInventoryItem["Company_Id"]) ? trim($updatedInventoryItem["Company_Id"]) : "";
            $Product_Id = !empty($updatedInventoryItem) && isset($updatedInventoryItem["Product_Id"]) ? trim($updatedInventoryItem["Product_Id"]) : "";
            $existingInventory = Inventory::where(["Product_Id" => $Product_Id, "Company_Id" => $Company_Id])->select('Inventory_Id')->first();
            $Inventory_Id = !empty($existingInventory) && isset($existingInventory["Inventory_Id"]) ? trim($existingInventory["Inventory_Id"]) : "";

            if (!empty($existingInventory)) {
                DB::table('dbo.inventory')
                        ->where(["Inventory_Id" => $Inventory_Id])
                        ->update($updatedInventoryItem);
                $updateCnt++;
            } else {
                Inventory::insert($updatedInventoryItem);
                $insertCnt++;
            }
        }


        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $output_message = "Added new  $insertCnt and updated $updateCnt records of Inventory data successfully at " . now();

        echo PHP_EOL . $output_message . PHP_EOL;
        echo PHP_EOL . "Process Completed ..... " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
    }

}
