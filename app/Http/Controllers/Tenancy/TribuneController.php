<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\Tribune;
use App\Models\Tenancy\TribuneUsers;
use App\Services\TribuneService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TribuneController extends Controller
{
    public function __construct(protected TribuneService $service)
    {
    }

    public function index(Request $request)
    {
        return Inertia::render('Tenancy/Tribunes/Index', [
            'tribunes' => $this->service->getAllTribunes($request),
            'filters' => $request->only(['sort', 'direction', 'search']),
        ]);
    }

    public function edit(int $id)
    {
        $tribune = Tribune::findOrFail($id);
        

        return Inertia::render('Tenancy/Tribunes/EditMember', [
            'tribuneData' => $this->service->getTribuneDetails($tribune),
        ]);
    }

    public function removeUser(int $id)
    {
        $tribuneUser = TribuneUsers::findOrFail($id);
        $this->service->removeUserFromTribune($tribuneUser);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}