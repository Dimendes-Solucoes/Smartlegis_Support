<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTerm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'mandate_id',
        'category_party_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mandate()
    {
        return $this->belongsTo(Mandate::class, 'mandate_id');
    }

    public function party()
    {
        return $this->belongsTo(CategoryParty::class, 'category_party_id');
    }
}
