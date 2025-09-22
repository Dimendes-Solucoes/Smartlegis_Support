<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class ProtocolReservation extends Model
{
    protected $fillable = [
        'user_id',
        'document_category_id',
        'protocol_number',
        'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }
}
