<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryParty extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_party',
        'timer'
    ];
}
