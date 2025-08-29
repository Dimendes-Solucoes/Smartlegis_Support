<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentStatusVote extends Model
{
    public const PENDENTE = 1;
    public const AGUARDANDO = 2;
    public const EM_VISTA = 3;
    public const EM_VOTACAO = 4;
    public const CONCLUIDO = 5;
    public const LEITURA = 6;

    use SoftDeletes;

    protected $table = 'document_status_vote';

    protected $fillable = [
        'name',
    ];

}