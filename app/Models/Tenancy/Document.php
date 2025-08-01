<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $table = 'documents';

    protected $fillable = [
        'name',
        'attachment',
        'carimbo_camara_pdf',
        'carimbo_prefeitura_pdf',
        'parecer_procurador_pdf',
        'document_category_id',
        'document_status_vote_id',
        'document_status_movement_id',
        'doc_key_clicksign',
        'protocol_number',
        'pedido_de_vista',
        'motivo_pedido_vista',
        'votes_are_secret',
        'voting_result_1',
        'voting_result_2',
        'status_sign',
        'attachment_partial',
        'decision_message',
    ];

    protected $casts = [
        'votes_are_secret' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'document_sessions')
            ->using(DocumentSession::class)
            ->withPivot('ordem_do_dia', 'order')
            ->withTimestamps();
    }
}
