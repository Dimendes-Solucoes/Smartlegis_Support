<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentSession extends Pivot
{
    use SoftDeletes;

    protected $table = 'document_sessions';

    protected $fillable = [
        'session_id',
        'document_id',
        'ordem_do_dia',
        'order',
        'secret_vote',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}