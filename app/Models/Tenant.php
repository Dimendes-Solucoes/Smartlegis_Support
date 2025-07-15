<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }
}
