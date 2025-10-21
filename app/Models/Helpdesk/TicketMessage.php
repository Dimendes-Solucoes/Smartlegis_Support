<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'tenant_id',
        'author_id',
        'author_name',
        'content',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}