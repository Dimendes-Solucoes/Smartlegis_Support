<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BigDiscussionUsers extends Model
{
    use SoftDeletes;

    public const AGUARDANDO = 1;
    public const FALANDO = 2;
    public const PAUSADO = 3;
    public const BACK_PAUSE = 4;
    public const FINALIZADO = 5;

    public const STATUS_FALA = [
        self::AGUARDANDO,
        self::FALANDO,
        self::PAUSADO,
        self::BACK_PAUSE,
        self::FINALIZADO,
    ];

    protected $fillable = [
        'user_id',
        'big_discussion_id',
        'timer',
        'time_extra',
        'status_big_discussion',
        'status_fala_big_discussion'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bigDiscussion()
    {
        return $this->belongsTo(BigDiscussion::class);
    }
}
