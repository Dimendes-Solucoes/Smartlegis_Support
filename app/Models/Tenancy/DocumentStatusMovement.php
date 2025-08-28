<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentStatusMovement extends Model
{
    public const ENVIADO_SECRETARIA = 1;
    public const EM_SESSAO = 2;
    public const ENVIADO_PROCURADOR = 3;
    public const ENVIADO_COMISSAO_JUSTICA = 4;
    public const ENVIADO_COMISSOES = 5;
    public const ENVIADO_PREFEITURA = 6;
    public const EM_ANALISE = 7;
    public const ANALISE_REPROVADA = 8;

    use SoftDeletes;

    protected $table = 'document_status_movement';

    protected $fillable = [
        'name',
    ];
    
}