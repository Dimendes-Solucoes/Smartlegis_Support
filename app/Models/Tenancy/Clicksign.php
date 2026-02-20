<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class Clicksign extends Model
{
    protected $fillable = [
        'body',
        'source',
        'uri',
        'server'
    ];
}
