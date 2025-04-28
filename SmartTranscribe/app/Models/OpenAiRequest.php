<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenAiRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'input',
        'model',
        'content',
        'tokens'
    ];

    protected $casts = [
        'tokens' => 'json'
    ];
}
