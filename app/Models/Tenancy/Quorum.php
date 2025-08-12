<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Quorum extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'session_id'
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, QuorumUsers::class)
            ->whereNull('quorum_users.deleted_at');
    }

    public function quorumUsers()
    {
        return $this->hasMany(QuorumUsers::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function bigDiscussions()
    {
        return $this->hasMany(BigDiscussion::class);
    }

    public function tribunes()
    {
        return $this->hasMany(Tribune::class);
    }

    public function questionOrders()
    {
        return $this->hasMany(QuestionOrder::class);
    }

    protected static function booted()
    {
        static::deleting(function ($quorum) {
            DB::transaction(function () use ($quorum) {
                $quorum->quorumUsers()->delete();
                $quorum->discussions()->delete();
                $quorum->bigDiscussions()->delete();
                $quorum->tribunes()->delete();
                $quorum->questionOrders()->delete();
            });
        });
    }
}
