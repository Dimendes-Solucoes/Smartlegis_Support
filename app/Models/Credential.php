<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credential extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'tenant_id',
        'short_name',
        'channel',
        'host',
        'key',
        'city_name'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
