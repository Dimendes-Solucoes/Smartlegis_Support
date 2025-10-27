<?php

namespace App\Models\Helpdesk;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketTenant extends Pivot
{
    protected $table = 'ticket_tenants';

    public $timestamps = false;
    
    public $incrementing = true;
}