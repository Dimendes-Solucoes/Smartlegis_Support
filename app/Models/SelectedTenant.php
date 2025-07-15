<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectedTenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tenant_id'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
