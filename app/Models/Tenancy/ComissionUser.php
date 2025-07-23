<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class ComissionUser extends Model
{
    public const PRESIDENT = 1;
    public const VICE = 2;
    public const REPORTER = 3;
    public const BOARD = 4;

    public const FUNCTIONS = [
        self::PRESIDENT,
        self::VICE,
        self::REPORTER,
        self::BOARD,
    ];

    protected $fillable = [
        'comission_id',
        'user_id',
        'function'
    ];
}
