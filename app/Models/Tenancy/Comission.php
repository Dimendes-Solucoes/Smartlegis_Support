<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comission extends Model
{
    use SoftDeletes;

    public const COMISSION = 1;
    public const BOARD = 2;

    public const TYPES = [
        self::COMISSION,
        self::BOARD,
    ];

    public const LIST_TYPES = [
        [
            'id' => self::COMISSION,
            'title' => 'Comissão',
        ],
        [
            'id' => self::BOARD,
            'title' => 'Mesa Diretora'
        ]
    ];

    protected $fillable = [
        'comission_name',
        'type'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'comission_users', 'comission_id', 'user_id')
            ->withPivot('function');
    }
}
