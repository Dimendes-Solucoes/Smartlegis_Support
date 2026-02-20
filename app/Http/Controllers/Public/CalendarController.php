<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct(
        protected CalendarService $service
    ) {}

    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $events = $this->service->getAllTenantsMonthlySessions((int) $year, (int) $month);

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        $calendarData = [
            'year' => (int) $year,
            'month' => (int) $month,
            'monthName' => $monthName,
            'events' => $events,
        ];

        return Inertia::render('Public/Calendar/Index', [
            'calendarData' => $calendarData,
        ]);
    }
}
