<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_tag',
        'serial_number',
        'model',
        'category_id',
        'status',
        'assigned_to',
        'asset_flow',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }
}
