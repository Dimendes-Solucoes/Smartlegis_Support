<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class TribuneUsers extends Model
{
    public const AGUARDANDO = 1;
    public const FALANDO = 2;
    public const PAUSADO = 3;
    public const BACK_PAUSE = 4;
    public const FINALIZADO = 5;
    public const EXTRA = 6;

    public const STATUS_FALA = [
        self::AGUARDANDO,
        self::FALANDO,
        self::PAUSADO,
        self::BACK_PAUSE,
        self::FINALIZADO,
        self::EXTRA
    ];

    protected $fillable = [
        'user_id',
        'tribune_id',
        'timer',
        'time_extra',
        'status_fala',
        'type',
        'position'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tribune()
    {
        return $this->belongsTo(Tribune::class);
    }
}
