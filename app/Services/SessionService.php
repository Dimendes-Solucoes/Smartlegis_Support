<?php

namespace App\Services;

use App\Models\Tenancy\Session;
use Illuminate\Support\Collection;

class SessionService
{
    public function getAllSessions(): Collection
    {
        return Session::get()->map(fn ($session) => [
            'id' => $session->id,
            'name' => $session->name,
            'datetime_start' => $session->datetime_start,
            'session_status_id' => $session->session_status_id,
        ]);
    }
}