<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionStatus extends Model
{
    use SoftDeletes;

    public const SESSION_STATUS_AGUARDANDO_VOTACAO = 1;
    public const SESSION_STATUS_EM_VOTACAO = 2;
    public const SESSION_STATUS_CONCLUIDA = 3;

    protected $fillable = [
        'name'
    ];
}
