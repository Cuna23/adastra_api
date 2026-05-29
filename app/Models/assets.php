<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'category_id',
        'status',
        'asset_tag',
        'serial_number',
        'emp_id',
        'department',
        'approved_by',
        'purchased_by',
        'assigned_to',
        'remark',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }
}