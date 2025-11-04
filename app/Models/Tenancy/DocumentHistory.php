<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_histories';

    const TYPE_ORIGINAL = '1';

    protected $fillable = [
        'document_id',
        'attachment',
        'type',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}