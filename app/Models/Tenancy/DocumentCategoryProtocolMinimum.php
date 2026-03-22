<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentCategoryProtocolMinimum extends Model
{
    protected $fillable = [
        'document_category_id',
        'year',
        'min_protocol',
    ];

    protected $casts = [
        'year'         => 'integer',
        'min_protocol' => 'integer',
    ];

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }
}
