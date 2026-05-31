<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocketEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'socket_id',
        'payload',
        'active'
    ];
}
