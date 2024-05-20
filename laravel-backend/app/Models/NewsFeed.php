<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{

    public $table = 'news_feed';
    public $primaryKey = 'news_feed_id';
    public $fillable = ['description', 'inserted_by', 'is_active', 'lastchanged_by', 'module_id', 'name', 'news_feed_id'];
    public $casts = ['is_active' => 'boolean', 'name' => 'string'];
    public $hidden = [];
    public $appends = [];

}
