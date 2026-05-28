<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UrgentDocument extends Model
{
    use SoftDeletes;

    const VOTE_STATUS_PENDING = '0';
    const VOTE_STATUS_VOTING = '1';
    const VOTE_STATUS_FINISHED = '2';

    const VOTE_RESULT_APPROVED = '1';
    const VOTE_RESULT_REJECTED = '2';
    const VOTE_RESULT_ABSTENTION = '3';

    const VOTE_STATUS = [
        self::VOTE_STATUS_PENDING,
        self::VOTE_STATUS_VOTING,
        self::VOTE_STATUS_FINISHED,
    ];

    const VOTE_RESULT = [
        self::VOTE_RESULT_APPROVED,
        self::VOTE_RESULT_REJECTED,
        self::VOTE_RESULT_ABSTENTION,
    ];

    protected $fillable = [
        'document_id',
        'comission_id',
        'vote_status',
        'vote_result',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function comission()
    {
        return $this->belongsTo(Comission::class);
    }

    public function votes()
    {
        return $this->hasMany(UrgentDocumentVote::class);
    }
}
