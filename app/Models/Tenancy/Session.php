<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Collection;
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

    public function getDocumentsData(): Collection
    {
            return DB::table('document_sessions')
                ->join('documents', 'document_sessions.document_id', '=', 'documents.id')
                ->where('document_sessions.session_id', $this->id) // usa o ID da instância atual da sessão
                ->select(
                    'documents.id',
                    'documents.name',
                    'documents.attachment',
                    'document_sessions.ordem_do_dia',
                    'document_sessions.order'
                )
                ->orderBy('document_sessions.order', 'asc')
                ->get();
    }

    protected static function booted()
    {
        static::deleting(function ($session) {
            $session->documentSessions()->delete();
        });
    }
}
