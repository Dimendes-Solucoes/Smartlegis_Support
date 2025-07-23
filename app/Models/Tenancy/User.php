<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    public const USUARIO_VEREADOR = 1;
    public const USUARIO_INVISIVEL = 2;
    public const USUARIO_ENVIO_DOCUMENTO = 3;

    protected $fillable = [
        'name',
        'nickname',
        'email',
        'path_image',
        'password',
        'user_category_id',
        'category_party_id',
        'status_user',
        'status_lider',
        'signer_key_clicksign',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pivot'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status_lider' => 'boolean'
    ];

    public function party()
    {
        return $this->belongsTo(CategoryParty::class, 'category_party_id');
    }

    public function category()
    {
        return $this->belongsTo(UserCategory::class, 'user_category_id');
    }

    public function terms()
    {
        return $this->hasMany(UserTerm::class);
    }
}
