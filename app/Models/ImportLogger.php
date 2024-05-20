<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLogger extends Model
{

    public $table = 'import_logger';
    public $primaryKey = 'id';
    public $fillable = [
        'comment',
        'filename',
        'filesize',
        'imported_at',
        'imported_by',
        'original_filename',
        'output_message',
        'uploaded_at',
    ];
    public $casts = ['filename' => 'string', 'filesize' => 'string', 'original_filename' => 'string'];
    public $hidden = ['PriceData'];
    public $appends = [];

    public function PriceData()
    {
        return $this->hasMany(PriceDatum::class, 'logger_id');
    }

}
