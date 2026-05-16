<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalNorm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'object',
        'number',
        'attachment',
        'publication_date',
        'norm_type_id',
        'norm_subject_id',
        'is_active',
    ];

    protected $casts = [
        'publication_date' => 'date:Y-m-d',
        'is_active'        => 'boolean',
    ];

    public function normType()
    {
        return $this->belongsTo(NormType::class);
    }

    public function normSubject()
    {
        return $this->belongsTo(NormSubject::class);
    }
}
