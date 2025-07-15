<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // usar o SoftDeletes do bd

class DocumentCategory extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'name',
        'abbreviation',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}