<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class NormSubject extends Model
{
    protected $fillable = ['name'];

    public function legalNorms()
    {
        return $this->hasMany(LegalNorm::class);
    }
}
