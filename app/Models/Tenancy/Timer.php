<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    protected $fillable = [
        'timer',
        'locale',
        'title',
        'order'
    ];
}
