<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Depot from Dataware House
 */
class DwDepot extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staging.dw_Depot';
	protected $primaryKey = 'Depot_Id';
}
