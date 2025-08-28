<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentStatusVote extends Model
{
    public const AGUARDANDO_MOVIMENTACAO = 1;
    public const AGUARDANDO_VOTACAO = 2;
    public const EM_VISTA = 3;
    public const EM_VOTACAO = 4;
    public const VOTACAO_CONCLUIDA = 5;
    public const LEITURA = 6;

    use SoftDeletes;

    protected $table = 'document_status_vote';

    protected $fillable = [
        'name',
    ];

}