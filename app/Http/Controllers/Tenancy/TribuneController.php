<?php

// CORREÇÃO: A sintaxe de namespace foi corrigida de '.' para '\'
namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\Tribune;
use App\Models\Tenancy\TribuneUsers;
use App\Services\TribuneService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        
        return Inertia::render('Tenancy/Tribunes/Show', [
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