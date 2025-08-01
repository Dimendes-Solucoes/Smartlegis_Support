<?php

namespace App\Models\Tenancy;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use Compoships;

    protected $fillable = [
        'voto_nominal',
        'session_id',
        'document_id',
        'user_id',
        'vote_category_id',
        'order'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VoteCategory::class);
    }

    public function documentSession()
    {
        return $this->belongsTo(DocumentSession::class, ['document_id', 'session_id'], ['document_id', 'session_id']);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
