<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tribune extends Model
{
    use SoftDeletes;

    public const STATUS_SUBSCRIBE = 1;
    public const END_SUBSCRIBE = 2;
    public const ENDING_TRIBUNE = 3;

    public const STATUS_TRIBUNE = [
        self::STATUS_SUBSCRIBE,
        self::END_SUBSCRIBE,
        self::ENDING_TRIBUNE,
    ];

    public const TYPE_DEFAULT = 1;
    public const TYPE_GUEST = 2;

    public const TYPES = [
        self::TYPE_DEFAULT,
        self::TYPE_GUEST,
    ];

    protected $fillable = [
        'quorum_id',
        'status_tribune',
        'type'
    ];

    public function quorum()
    {
        return $this->belongsTo(Quorum::class);
    }

    public function tribuneUsers()
    {
        return $this->hasMany(TribuneUsers::class);
    }

    public function apartiamentoUsers()
    {
        return $this->hasMany(ApartiamentoUser::class);
    }

    protected static function booted()
    {
        static::deleting(function ($tribune) {
            $tribune->tribuneUsers()->delete();
            $tribune->apartiamentoUsers()->delete();
        });
    }
}
