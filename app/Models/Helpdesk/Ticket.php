<?php

namespace App\Models\Helpdesk;

use App\Enums\TicketStatus;
use App\Models\Credential;
use App\Models\User;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_type_id',
        'status',
        'author_id',
        'code',
        'title',
        'description',
    ];

    protected $casts = [
        'status' => TicketStatus::class
    ];

    protected $appends = ['status_details'];

    public function getStatusDetailsAttribute()
    {
        return [
            'value' => $this->status->value,
            'title' => $this->status->title(),
            'color' => $this->status->color(),
        ];
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function credentials()
    {
        return $this->belongsToMany(Credential::class, 'ticket_credentials')
            ->using(TicketCredential::class);
    }
}
