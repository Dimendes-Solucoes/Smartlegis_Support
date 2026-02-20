<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketCredential extends Pivot
{
    protected $fillable = [
        'ticket_id',
        'credential_id'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }
}
