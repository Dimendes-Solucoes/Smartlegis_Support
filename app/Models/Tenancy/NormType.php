<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class NormType extends Model
{
    protected $fillable = ['name', 'abbreviation'];

    public function legalNorms()
    {
        return $this->hasMany(LegalNorm::class);
    }
}
