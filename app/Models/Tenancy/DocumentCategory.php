<?php

namespace App\Models\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'order',
        'min_protocol',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'active_reserved_protocols',
        'next_available_protocol'
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_category_id');
    }

    public function protocols()
    {
        return $this->hasMany(ProtocolReservation::class);
    }

    public function getActiveReservedProtocolsAttribute()
    {
        return $this->protocols()
            ->where('expires_at', '>', now())
            ->pluck('protocol_number')
            ->toArray();
    }

    public function getNextAvailableProtocolAttribute()
    {
        $used_in_documents = $this->documents()
            ->whereRaw("CAST(protocol_number AS INTEGER) >= ?", [$this->min_protocol])
            ->whereRaw("protocol_number ~ '^[0-9]+$'")
            ->pluck('protocol_number')
            ->map(fn($protocol_number) => (int) $protocol_number)
            ->toArray();

        $active_reservations = $this->protocols()
            ->where('protocol_number', '>=', $this->min_protocol)
            ->where('expires_at', '>', now())
            ->pluck('protocol_number')
            ->toArray();

        $all_used_protocols = array_merge($used_in_documents, $active_reservations);
        $next_protocol = (int) $this->min_protocol;

        while (in_array($next_protocol, $all_used_protocols)) {
            $next_protocol++;
        }

        return $next_protocol;
    }
}
