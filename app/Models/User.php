<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

  protected function casts(): array
    {
        return [
            'password' => 'hashed',
            // ← buang email_verified_at sebab dah delete column tu
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
