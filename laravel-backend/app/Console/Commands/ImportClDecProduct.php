<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ClDesProduct;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Imports\ProductImport;
use App\PricingData;
use App\Supplier;
use App\ImportLogger;
use App\components\Helper;

class ImportClDecProduct extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cl_dec_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands imports the product data from the product csv file stored at '
            . 'public/upload/cl_dec_products which is provided by Sigma/Krishna';

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
     * reference : https://makitweb.com/import-csv-data-to-mysql-database-with-laravel/
     *
     * @return mixed
     */
    public function handle()
    {
        $dir = storage_path("app/public/upload/cl_des_products");
        $files = [];
        $import_method = ImportLogger::FILE_IMPORT;

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }

                    $files[] = $file;
                }
                closedir($dh);
            }
        }


        $valid_extension = array("csv", "xlsx", "xls");
        if (!empty($files)) {

            foreach ($files as $file) {
                if (!empty($file)) {
                    $filepath = !empty($file) ? $dir . "/" . $file : "";

                    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
                    if (in_array(strtolower($extension), $valid_extension)) {

                        try {

                            $headings = (new HeadingRowImport)->toArray($filepath);

                            $filesize = filesize($filepath);
                            preg_match('/user_(.*?)_id/', $file, $match);
                            $userId = !empty($match) && isset($match[1]) ? (int) trim($match[1]) : "";
                            $file_size = Helper::formatSizeUnits($filesize);

                            $heading = !empty($headings) && isset($headings[0][0][0]) ? trim($headings[0][0][0]) : "";

                            $importLogger = ImportLogger::where(["filename" => $file,
                                        'user_id' => $userId])->first();
                            $importLoggerId = !empty($importLogger) ? $importLogger->id : "";

                            if ($heading != "a_prod_code") {
                                $output_message = PHP_EOL . 'Invalid file format' . PHP_EOL;
                                echo $output_message;
                                ImportLogger::updateImportLog($importLoggerId, $file, $userId, $import_method, $output_message, $output_message);

                                return false;
                            }

                            $data = Excel::toArray(new ProductImport, $filepath);

                            $importData_arr = !empty($data) && isset($data[0]) ? $data[0] : [];

                                // Imports the product data into cl_des_products table from file input
                            $output_message = ClDesProduct::importProductData($importLoggerId, $file, $importData_arr, $userId);
                            echo PHP_EOL . $output_message . PHP_EOL;

                            unlink($filepath);
                            $user = \App\User::find($userId);

                            $data = ['output_message' => $output_message, 'user' => $user];
							
							\Artisan::call('map:products');
							
                            \Mail::send('emails.product_import', ['data' => $data], function($message) use ($user) {
                                $message->to($user->email, $user->getName())->subject('Products Imported');
                            });
                        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {

                            echo PHP_EOL . $e->getMessage() . " on line " . $e->getLine() . PHP_EOL;
                        }
                    } else {
                        echo PHP_EOL . 'Invalid File Extension.' . PHP_EOL;
                    }
                } else {
                    echo PHP_EOL . 'File is not available.' . PHP_EOL;
                }
            }
        } else {
            echo PHP_EOL . 'No any Product File to import.' . PHP_EOL;
        }
    }

}
