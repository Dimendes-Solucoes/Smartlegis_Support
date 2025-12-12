<?php

namespace App\Models\Tenancy;

use App\Models\Scopes\OrderById;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'cpf',
        'path_image',
        'birth',
        'gender',
        'registration',
        'role',
        'email',
        'password',
        'password_reset_token',
        'password_reset_expires',
        'clicksign_signer_key',
        'clicksign_secret',
        'status',
        'footer'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
        'password_reset_token',
        'password_reset_expires',
        'clicksign_signer_key',
        'clicksign_secret',
        'pivot'
    ];

    protected $with = [
        'departments',
        'abilities'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'registration' => 'string',
    ];

    protected $appends = ['has_signer_key'];

    public const PENDING = 0;
    public const ACTIVE = 1;
    public const CANCELED = 2;

    public const STATUS_LIST = [
        self::PENDING,
        self::ACTIVE,
        self::CANCELED
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new OrderById);
    }

    protected function getHasSignerKeyAttribute()
    {
        return !empty($this->clicksign_signer_key) && !empty($this->clicksign_secret);
    }

    public function isLeader()
    {
        return $this->departments()->wherePivot('is_leader', true)->exists();
    }
}
