<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class QuestionOrderUsers extends Model
{
    public const WAITING = 1;
    public const TALKING = 2;
    public const PAUSED = 3;
    public const BACK_PAUSE = 4;
    public const FINISHED = 5;

    const STATUS_FALA = [
        self::WAITING,
        self::TALKING,
        self::PAUSED,
        self::BACK_PAUSE,
        self::FINISHED,
    ];

    protected $fillable = [
        'question_order_id',
        'user_id',
        'timer',
        'status_fala',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questionOrder()
    {
        return $this->belongsTo(QuestionOrder::class);
    }
}
