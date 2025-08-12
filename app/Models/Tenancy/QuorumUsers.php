<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class QuorumUsers extends Model
{
    protected $fillable = [
        'user_id',
        'quorum_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quorum()
    {
        return $this->belongsTo(Quorum::class);
    }
}
