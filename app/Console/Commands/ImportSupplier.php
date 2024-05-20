<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

/**
 * This class file is created to write a function to import Supplier data from staging to main table of product portal
 */
class ImportSupplier extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands fetches latest records from dw_Supplier data and stores in main table "supplier" of product portal';

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
        $insertCnt = $lstSupplierStagingCnt = $updateCnt = 0;
        echo PHP_EOL . "Process Started ..... " . PHP_EOL;
        //Get the latest date of inserted record from Supplier staging table
        $latestStagingDateObj = DB::table('staging.dw_Supplier')
                ->select(DB::raw('max(Inserted_DateTime) as maxInDate'))
                ->first();
        $latestStagingDate = !empty($latestStagingDateObj) ? trim($latestStagingDateObj->maxInDate) : "";

        //Get the latest date of inserted record from Supplier main table
        $latestDateObj = DB::table('dbo.supplier')
                ->select(DB::raw('max(Inserted_DateTime) as maxInDate'))
                ->first();
        $latestDate = !empty($latestDateObj) ? trim($latestDateObj->maxInDate) : "";

        if ($latestStagingDate != $latestDate) {
            //Get the latest inserted records from staging Supplier table

            $stagingSuppliers = DB::select(DB::raw("select Company_Id,Currency_Id,Supplier_Code,Supplier_Name,Supplier_Add1,Supplier_Add2,Supplier_Add3,Supplier_PostCOde,Supplier_Contact,Supplier_TelNo,Supplier_Email,Buyer_Code,Group_Code,Category_Code,Supplier_Contact_TelNo,Supplier_Contact_Mobile,Supplier_Contact_Email,Stop_Ind,LastPaid_Date,LastPaid_Amount,Inserted_DateTime,Updated_DateTime,Inserted_By,Updated_By from staging.dw_Supplier where Inserted_DateTime = (select max(Inserted_DateTime) from staging.dw_Supplier)"));
            $stagingSuppliersData = json_decode(json_encode($stagingSuppliers), true);

            //Get the total count of latest inserted from staging Supplier table
            $lstSupplierStagingCnt = count($stagingSuppliersData);

            $insertCnt = $lstSupplierStagingCnt;
            foreach (array_chunk($stagingSuppliersData, (2100 / 44) - 2) as $chunk) {
                Supplier::insert($chunk);
            }
            echo PHP_EOL . "inserted fresh  $insertCnt records" . PHP_EOL;
        }


        //Update existing record
        $updateCnt = 0;
        //Get the latest updated records from staging GRN table
        $updatedSupplier = DB::select(DB::raw("select Company_Id,Currency_Id,Supplier_Code,Supplier_Name,Supplier_Add1,Supplier_Add2,Supplier_Add3,Supplier_PostCOde,Supplier_Contact,Supplier_TelNo,Supplier_Email,Buyer_Code,Group_Code,Category_Code,Supplier_Contact_TelNo,Supplier_Contact_Mobile,Supplier_Contact_Email,Stop_Ind,LastPaid_Date,LastPaid_Amount,Inserted_DateTime,Updated_DateTime,Inserted_By,Updated_By from staging.dw_Supplier where Updated_DateTime = (select max(Updated_DateTime) from staging.dw_Supplier)"));

        $updatedSupplierItems = json_decode(json_encode($updatedSupplier), true);

        foreach ($updatedSupplierItems as $updatedSupplierItem) {
            $Supplier_Name = !empty($updatedSupplierItem) && isset($updatedSupplierItem["Supplier_Name"]) ? trim($updatedSupplierItem["Supplier_Name"]) : "";
            $Supplier_Code = !empty($updatedSupplierItem) && isset($updatedSupplierItem["Supplier_Code"]) ? trim($updatedSupplierItem["Supplier_Code"]) : "";
            $existingSupplier = Supplier::where(["Supplier_Code" => $Supplier_Code, "Supplier_Name" => $Supplier_Name])->select('Supplier_Id')->first();
            $Supplier_Id = !empty($existingSupplier) && isset($existingSupplier["Supplier_Id"]) ? trim($existingSupplier["Supplier_Id"]) : "";

            if (!empty($existingSupplier)) {
                DB::table('dbo.Supplier')
                        ->where(["Supplier_Id" => $Supplier_Id])
                        ->update($updatedSupplierItem);
                $updateCnt++;
            } else {
                Supplier::insert($updatedSupplierItem);
                $insertCnt++;
            }
        }


        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $output_message = "Added new  $insertCnt and updated $updateCnt records of Supplier data successfully at " . now();

        echo PHP_EOL . $output_message . PHP_EOL;
        echo PHP_EOL . "Process Completed ..... " . PHP_EOL;
        echo PHP_EOL . 'Total Execution Time: ' . (($execution_time * 1000 / 1000)) . ' sec' . PHP_EOL;
    }

}
