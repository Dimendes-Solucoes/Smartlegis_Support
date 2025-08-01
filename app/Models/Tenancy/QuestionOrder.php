<?php

namespace App\Models\Tenancy;

class QuestionOrder extends BaseModel
{
    public const SUBSCRIBE = 1;
    public const END_SUBSCRIBE = 2;
    public const ENDING_QUESTION_ORDER = 3;

    protected $fillable = [
        'quorum_id',
        'status'
    ];

    public const STATUS = [
        self::SUBSCRIBE,
        self::END_SUBSCRIBE,
        self::ENDING_QUESTION_ORDER,
    ];

    public function quorum()
    {
        return $this->belongsTo(Quorum::class);
    }

    public function questionOrderUsers()
    {
        return $this->hasMany(QuestionOrderUsers::class);
    }

    protected static function booted()
    {
        static::deleting(function ($discussion) {
            $discussion->questionOrderUsers()->delete();
        });
    }
}
