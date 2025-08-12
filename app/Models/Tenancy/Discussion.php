<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    public const STATUS_SUBSCRIBE = 1;
    public const END_SUBSCRIBE = 2;
    public const ENDING_DISCUSSION = 3;

    public const STATUS_DISCUSSION = [
        self::STATUS_SUBSCRIBE,
        self::END_SUBSCRIBE,
        self::ENDING_DISCUSSION,
    ];

    protected $fillable = [
        'quorum_id',
        'document_id',
        'status_discussion'
    ];

    public function quorum()
    {
        return $this->belongsTo(Quorum::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function discussionUsers()
    {
        return $this->hasMany(DiscussionUsers::class);
    }
}
