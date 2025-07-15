<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'website_name',
        'webhook_url',
        'token'
    ];

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }
}
