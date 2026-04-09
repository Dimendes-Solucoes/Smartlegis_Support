<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'request_signature_key',
        'status_sign',
        'list_key'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
