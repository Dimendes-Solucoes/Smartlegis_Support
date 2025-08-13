<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class BigDiscussion extends Model
{
    public const STATUS_SUBSCRIBE = 1;
    public const END_SUBSCRIBE = 2;
    public const ENDING_BIG_DISCUSSION = 3;

    public const STATUS_BIG_DISCUSSION = [
        self::STATUS_SUBSCRIBE,
        self::END_SUBSCRIBE,
        self::ENDING_BIG_DISCUSSION,
    ];

    protected $fillable = [
        'quorum_id',
        'status_big_discussion'
    ];

    public function quorum()
    {
        return $this->belongsTo(Quorum::class);
    }

    public function bigDiscussionUsers()
    {
        return $this->hasMany(BigDiscussionUsers::class);
    }
}
