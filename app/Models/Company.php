<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';

    protected $fillable = [
        'type',
        'title',
        'image_path',
        'content',
        'sort_order',
        'uploaded_by'
    ];
}
