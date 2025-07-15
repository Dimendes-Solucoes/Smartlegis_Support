<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'session_status_notify',
        'session_status_id',
        'datetime_start',
        'datetime_end',
    ];

    protected $casts = [
        'datetime_start' => 'datetime',
        'datetime_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(SessionStatus::class);
    }
}
