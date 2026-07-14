<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestLog extends Model
{
    protected $fillable = [
        'service_request_id',
        'user_id',
        'action',
        'description',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}