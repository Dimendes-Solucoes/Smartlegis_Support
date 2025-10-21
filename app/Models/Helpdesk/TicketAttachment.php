<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'tenant_id',
        'user_id',
        'file_name',
        'file_path',
    ];
    
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}