<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'position',
        'department_id',
        'background',
        'photo_path',
        'sort_order',
        'uploaded_by',
    ];

    protected $appends = ['department_name'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department?->department_name;
    }
}