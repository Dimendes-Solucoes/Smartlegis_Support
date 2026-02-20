<?php

namespace App\Services;

use App\Models\Tenancy\Session;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CalendarService
{
    public function getAllTenantsMonthlySessions(int $year, int $month): array
    {
        $tenants = Tenant::with('credentials')->get();

        $allSessionsData = [];

        foreach ($tenants as $tenant) {
            $dbName = $tenant->data['tenancy_db_name'];

            if (empty($dbName)) {
                continue;
            }

            try {
                DB::statement("SET search_path TO {$dbName}");

                $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();

                $sessions = Session::whereBetween('datetime_start', [$startDate, $endDate])
                    ->with(['user', 'status'])
                    ->orderBy('datetime_start')
                    ->get();

                $formattedSessions = $sessions->map(function ($session) use ($tenant) {
                    return [
                        'id' => $session->id,
                        'title' => $session->name,
                        'start' => $session->datetime_start->toISOString(),
                        'end' => $session->datetime_end ? $session->datetime_end->toISOString() : null,
                        'status_id' => $session->session_status_id,
                        'status_name' => $session->status->name ?? 'N/A',
                        'tenant_id' => $tenant->id,
                        'tenant_city' => $tenant->credentials->first()->city_name ?? $tenant->id
                    ];
                })->toArray();

                $allSessionsData = array_merge($allSessionsData, $formattedSessions);
            } catch (\Exception $e) {
                continue;
            }
        }

        $events = $this->orderEvents($allSessionsData);

        return $events;
    }

    private function orderEvents($events)
    {
        usort($events, function ($a, $b) {
            if ($a['status_id'] !== $b['status_id']) {
                return $b['status_id'] <=> $a['status_id'];
            }

            return $a['tenant_city'] <=> $b['tenant_city'];
        });

        return $events;
    }
}
