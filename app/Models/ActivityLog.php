<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    public $table = 'activity_logs';
    public $primaryKey = 'id';
   /**

     * The attributes that are mass assignable.

     *

     * @var arrays

     */

    protected $fillable = [

     'activity','description', 'url', 'method', 'ip', 'agent', 'user_id','created_at','read_at'

    ];
    public $casts = ['activity' => 'string','description' => 'string'];


}
