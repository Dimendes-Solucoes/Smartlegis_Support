<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_sessions';

    protected $fillable = [
        'session_id',
        'document_id',
        'ordem_do_dia',
        'order',
        'secret_vote',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}