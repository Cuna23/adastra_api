<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'ticket_no',
        'user_id',
        'subject',
        'description',
        'category',
        'priority',
        'attachment',
        'attachment_name',
        'status',
        'assigned_to',
        'resolution',
        'resolved_at',
        'closed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logs()
    {
        return $this->hasMany(IncidentLog::class);
    }
}
