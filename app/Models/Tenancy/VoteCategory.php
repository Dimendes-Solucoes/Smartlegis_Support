<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteCategory extends Model
{
    use SoftDeletes;

    public const A_FAVOR = 1;
    public const CONTRA = 2;
    public const ABSTENTION = 3;
    public const NOT_INFORMED = 4;

    protected $fillable = [
        'name'
    ];
}
