<?php

namespace App\Components;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Helper
{

    /**
     * Generates Random Password
     *
     * @return string The Password
     */
    public static function getRandomPassword()
    {
        $password = "";
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $passStr = !empty($pass) ? implode($pass) : '';
        $password = $passStr . "@1";
        return $password;
    }

    /**
     * Get Working historical date
     *
     * @return string The Password
     */
    public static function GetWorkingHisDate($holidays, $num)
    {
        $workingYesterday = '';
        $date = Carbon::now()->format('Y-m-d H:i:s');

        $MyDateCarbon = Carbon::parse($date);

        $MyDateCarbon->addWeekdays($num);
        $cnt = -($num);
        for ($i = 1; $i >= $cnt; $i--) {

            if (in_array(Carbon::parse($date)->addWeekdays($i)->toDateString(), $holidays)) {
                $MyDateCarbon->addDay();
            }
        }

        $workingYesterday = !empty($MyDateCarbon) ? $MyDateCarbon->format('Y-m-d') : '';
        return $workingYesterday;
    }

    /**
     * Generate PO number
     *
     * @return string The PO number
     */
    public static function generatePONumber($poIds)
    {

        $max = max($poIds);
        $length = self::countDigits($max);

        $zeroCount = 6 - $length;
        $postr = implode("_", $poIds);
        if ($zeroCount == 0 || $zeroCount < 0) {
            $filename = "PP" . $postr;
        }
        if ($zeroCount == 1) {
            $filename = "PP0" . $postr;
        }
        if ($zeroCount == 2) {
            $filename = "PP00" . $postr;
        }
        if ($zeroCount == 3) {
            $filename = "PP000" . $postr;
        }
        if ($zeroCount == 4) {
            $filename = "PP0000" . $postr;
        }
        if ($zeroCount == 5) {
            $filename = "PP00000" . $postr;
        }

        return $filename;
    }

    private static function countDigits($MyNum)
    {
        $MyNum = (int) abs($MyNum);
        $MyStr = strval($MyNum);
        return strlen($MyStr);
    }

    public static function getLatest3MonthDates($productParentCode, $sourceId)
    {
        $latestDates = [];
        $latestDates = DB::table('pricing_data')
                        ->select(DB::raw("FORMAT(price_from_date, 'yyyy-MM') AS year_month"))
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                        ->groupBy(DB::raw("FORMAT(price_from_date, 'yyyy-MM')"))
                        ->orderByDesc('year_month')
                        ->take(3)
                        ->pluck('year_month')->toArray();

        return $latestDates;
    }
    
    
    public static function getLatest14MonthDates($productParentCode, $sourceId)
    {
        $latestDates = [];
        $latestDates = DB::table('competitor_prices')
                        ->select(DB::raw("FORMAT(AsOfDate, 'yyyy-MM') AS year_month"))
                        ->where(["pricing_data.parent_product_code" => $productParentCode, "source_id" => $sourceId])
                        ->groupBy(DB::raw("FORMAT(AsOfDate, 'yyyy-MM')"))
                        ->orderByDesc('year_month')
                        ->take(14)
                        ->pluck('year_month')->toArray();

        return $latestDates;
    }

}
