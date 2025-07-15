<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenAiRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'input',
        'model',
        'content',
        'tokens'
    ];

    protected $casts = [
        'tokens' => 'json'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
