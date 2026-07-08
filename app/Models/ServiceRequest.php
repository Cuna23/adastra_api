<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sr_number',
        'request_title',
        'request_type',
        'category',
        'quantity',
        'priority',
        'description',
        'needed_by_date',
        'attachment',
        'attachment_name',
        'status',
        'requester_id',
        'approver_id',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'needed_by_date' => 'date',
        'reviewed_at' => 'datetime',
        'quantity' => 'integer',
    ];

    // Relationships
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Auto-generate SR number on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($serviceRequest) {
            $serviceRequest->sr_number = self::generateSrNumber();
        });
    }

    protected static function generateSrNumber(): string
    {
        $year = date('Y');
        $lastRequest = self::where('sr_number', 'like', "SR-{$year}-%")
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastRequest
            ? ((int) substr($lastRequest->sr_number, -4)) + 1
            : 1;

        return sprintf('SR-%s-%04d', $year, $nextNumber);
    }
}