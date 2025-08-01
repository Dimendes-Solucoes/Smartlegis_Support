<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuorumUsers extends Model
{
    use SoftDeletes;

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
