<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingSource extends Model
{

    protected $table = 'sources';

    public const PRICING = 1;
    public const USAGE = 2;

}
