<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clicksign\ClicksignDestroyRequest;
use App\Services\ClicksignService;
use Inertia\Inertia;

class ClicksignController extends Controller
{
    public function __construct(
        protected ClicksignService $service
    ) {}

    public function index()
    {
        $clicksigns = $this->service->getAll();

        return Inertia::render('Clicksign/Index', [
            'clicksigns' => $clicksigns,
        ]);
    }

    public function destroy(ClicksignDestroyRequest $request)
    {
        $this->service->clearCity($request->tenant_id);

        return redirect()->route('clicksign.index')
            ->with('success', 'Registros limpos com sucesso!');
    }
}
