<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use SoftDeletes;

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

    public function documentSessions()
    {
        return $this->hasMany(DocumentSession::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_sessions')
            ->using(DocumentSession::class)
            ->withPivot('ordem_do_dia', 'order')
            ->whereNull('document_sessions.deleted_at')
            ->orderBy('document_sessions.order', 'asc');
    }
}
