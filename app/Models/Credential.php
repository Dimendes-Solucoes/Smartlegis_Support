<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = [
        'tenant_id',
        'short_name',
        'channel',
        'host',
        'key',
        'city_name',
        'city_shield',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
