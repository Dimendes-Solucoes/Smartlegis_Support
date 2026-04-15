<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSignatureComission extends Model
{
    use HasFactory;

    protected $fillable = [
        'comission_document_id',
        'user_id',
        'signature_key_comission',
        'status_sign_comission',
        'list_key_comission',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
