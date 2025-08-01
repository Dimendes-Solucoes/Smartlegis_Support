<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCategory extends Model
{
    use SoftDeletes;

    public const SECRETARIO = 1;
    public const VEREADOR = 2;
    public const PRESIDENTE = 3;
    public const PROCURADOR = 4;
    public const PREFEITURA = 5;
    public const TV = 6;
    public const CAMARA = 7;
    public const CONVIDADO = 8;
    public const CONTROLE = 9;

    public const LEGISLATIVO = [
        self::VEREADOR,
        self::PRESIDENTE
    ];

    protected $fillable = [
        'name',
    ];
}
