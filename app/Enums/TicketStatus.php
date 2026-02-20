<?php

namespace App\Enums;

enum TicketStatus: string
{
    case NEW        = 'new';
    case OPEN       = 'open';
    case PENDING    = 'pending';
    case RESOLVED   = 'resolved';
    case CLOSED     = 'closed';
    case CANCELED   = 'canceled';

    public function title(): string
    {
        return match ($this) {
            self::NEW      => 'Novo',
            self::OPEN     => 'Em Andamento',
            self::PENDING  => 'Aguardando Informações',
            self::RESOLVED => 'Resolvido',
            self::CLOSED   => 'Fechado',
            self::CANCELED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW      => '#3B82F6',
            self::OPEN     => '#F59E0B',
            self::PENDING  => '#6B7280',
            self::RESOLVED => '#10B981',
            self::CLOSED   => '#1F2937',
            self::CANCELED => '#EF4444',
        };
    }
}
