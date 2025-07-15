<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resume extends Model
{
    use SoftDeletes;

    public const MODEL_CUSTOM = 'custom';
    public const MODEL_RESUME = 'resume';
    public const MODEL_MINUTE = 'minute';

    public const MODELS = [
        self::MODEL_CUSTOM,
        self::MODEL_RESUME,
        self::MODEL_MINUTE
    ];

    protected $fillable = [
        'client_id',
        'content',
        'model'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
