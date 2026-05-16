<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class TempLegalNorm extends Model
{
    protected $fillable = [
        'object',
        'number',
        'attachment',
        'publication_date',
        'norm_type_id',
        'norm_subject_id',
        'is_active',
        'extraction_meta',
        'original_filename',
        'status',
        'created_by',
    ];

    protected $casts = [
        'publication_date' => 'date:Y-m-d',
        'is_active'        => 'boolean',
        'extraction_meta'  => 'array',
    ];

    public function normType()
    {
        return $this->belongsTo(NormType::class);
    }

    public function normSubject()
    {
        return $this->belongsTo(NormSubject::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
