<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legislature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'start_at',
        'end_at',
        'is_current',
    ];

    protected $casts = [
        'start_at'   => 'date',
        'end_at'     => 'date',
        'is_current' => 'boolean'
    ];

    public function userTerms()
    {
        return $this->hasMany(UserTerm::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, UserTerm::class, 'legislature_id', 'id', 'id', 'user_id');
    }

    public function comissions()
    {
        return $this->hasMany(Comission::class);
    }
}
