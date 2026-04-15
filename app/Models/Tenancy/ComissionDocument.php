<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;

class ComissionDocument extends Model
{
    protected $table = 'comission_document';

    protected $fillable = [
        'comission_id',
        'document_id',
        'parecer_pdf',
        'parecer_pdf_partial',
        'status_parecer',
        'status_leitura',
        'status_sign_comission',
        'doc_key_clicksign_comission'
    ];

    public const NOT_SIGNED = 0;
    public const SIGNED_CLICKSIGN = 1;
    public const SIGNED = 2;
    public const EXPIRED = 3;

    public const STATUS_SIGN_COMISSION = [
        self::NOT_SIGNED,
        self::SIGNED_CLICKSIGN,
        self::SIGNED,
        self::EXPIRED,
    ];

    public function comission()
    {
        return $this->belongsTo(Comission::class, 'comission_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }
}
