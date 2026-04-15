<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clicksign\ClicksignDestroyRequest;
use App\Http\Requests\Clicksign\ClicksignReportRequest;
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

        return Inertia::render('Public/Clicksign/Index', [
            'clicksigns' => $clicksigns,
        ]);
    }

    public function report(ClicksignReportRequest $request)
    {
        $isDev  = auth()->user()->is_dev;
        $report = $this->service->getReport($request->filters(), $isDev);

        return Inertia::render('Public/Clicksign/Report', [
            'report'  => $report,
            'filters' => $request->filters(),
            'is_dev'  => $isDev,
        ]);
    }

    public function destroy(ClicksignDestroyRequest $request)
    {
        $this->service->clearCity($request->tenant_id);

        return redirect()->route('clicksign.index')
            ->with('success', 'Registros limpos com sucesso!');
    }
}
