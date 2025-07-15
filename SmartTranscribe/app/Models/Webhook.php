<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_NOT_STARTED = '1';
    const STATUS_SUCCEEDED = '2';
    const STATUS_FAILED = '3';
    const STATUS_INATIVE = '4';

    const STATUSES = [
        self::STATUS_NOT_STARTED,
        self::STATUS_SUCCEEDED,
        self::STATUS_FAILED,
        self::STATUS_INATIVE,
    ];

    protected $fillable = [
        'client_id',
        'code',
        'webhook_external_id',
        'webhook_url',
        'webhook_status',
    ];

    protected $guarded = [
        'id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
