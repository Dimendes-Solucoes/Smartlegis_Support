<?php

namespace App\Services;

use App\Models\SelectedTenant;
use App\Models\Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function list()
    {
        return Client::all();
    }
}
