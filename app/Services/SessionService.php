<?php

namespace App\Services;

use App\Models\Tenancy\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class SessionService
{
    public function getAllSessions(): LengthAwarePaginator
    {
        $sortField = Request::input('sort', 'datetime_start');
        $sortDirection = Request::input('direction', 'desc');
        $sortableFields = ['name', 'datetime_start'];
        if (!in_array($sortField, $sortableFields)) {
            $sortField = 'datetime_start';
        }

        return Session::orderBy($sortField, $sortDirection) 
            ->paginate(15)
            ->through(fn($session) => [
                'id' => $session->id,
                'name' => $session->name,
                'datetime_start' => $session->datetime_start,
                'session_status_id' => $session->session_status_id,
            ]);
    }
}