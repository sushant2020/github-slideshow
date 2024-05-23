<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{

    public $table = 'inventory';
    public $primaryKey = 'Inventory_Id';
    public $fillable = [
        'Company_Id',
        'Product_Id',
        'Depot_Id',
        'LG_Date',
        'LS_Date',
        'Physical_Stock',
        'Allocation_Stock',
        'Allocation_After',
        'On_Order',
        'Backorder',
        'LG_Number',
        'LPP_Cost',
        'Avg_Cost',
        'True_Cost',
        'Min_Stock',
        'Std_Cost',
        'Max_Stock',
        'Average_usage',
        'Pick_Bin',
        'Average_usage_UOM',
        'Average_usage_Period',
        'Inserted_DateTime',
        'Updated_DateTime',
        'Inserted_By',
        'Updated_By',
    ];
    //public $casts = ['average_usage_period' => 'string', 'average_usage_uom' => 'string', 'pick_bin' => 'string'];
    public $hidden = [];
    public $appends = [];

}
