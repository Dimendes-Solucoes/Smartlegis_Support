<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;

    protected $table = 'ticket_status';

    protected $fillable = [
        'title',
        'color',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ticket_status_id');
    }
}