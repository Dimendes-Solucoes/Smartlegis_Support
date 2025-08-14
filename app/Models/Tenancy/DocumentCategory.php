<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'order',
        'min_protocol',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean'
    ];

    protected $appends = ['highest_protocol'];

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_category_id');
    }

    public function getHighestProtocolAttribute()
    {
        return $this->documents()->max('protocol_number') ?? null;
    }
}
