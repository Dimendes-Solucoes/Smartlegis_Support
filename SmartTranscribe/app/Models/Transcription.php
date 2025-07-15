<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transcription extends Model
{
    use SoftDeletes;

    const STATUS_UNDEFINED = '0';
    const STATUS_NOT_STARTED = '1';
    const STATUS_RUNNING = '2';
    const STATUS_SUCCEEDED = '3';
    const STATUS_FAILED = '4';

    protected $fillable = [
        'client_id',
        'code',
        'external_id',
        'filename',
        'content',
        'status',
        'audio'
    ];

    protected $guarded = [
        'id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
