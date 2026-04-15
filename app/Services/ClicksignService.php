<?php

namespace App\Services;

use App\Models\Tenancy\Author;
use App\Models\Tenancy\Clicksign;
use App\Models\Tenancy\ComissionDocument;
use App\Models\Tenancy\Document;
use App\Models\Tenancy\DocumentSignatureComission;
use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\DB;

class ClicksignService
{
    /**
     * Custo fixo por documento na versão 1.9 da Clicksign.
     */
    private const COST_PER_DOC_V19 = 1;

    public function getAll(): array
    {
        $tenants = Tenant::with('credentials')->get();

        $allClicksignData = [];

        foreach ($tenants as $tenant) {
            $dbName = $tenant->data['tenancy_db_name'];

            if (empty($dbName)) {
                continue;
            }

            try {
                DB::statement("SET search_path TO {$dbName}");

                $allClicksignData[] = [
                    'tenant_id'   => $tenant->id,
                    'tenant_city' => $tenant->credentials->first()->city_name ?? $tenant->id,
                    'quantity'    => Clicksign::count(),
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        return $this->orderCities($allClicksignData);
    }

    public function getReport(array $filters = []): array
    {
        $tenants    = Tenant::with('credentials')->get();
        $reportData = [];

        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date']   ?? null;

        foreach ($tenants as $tenant) {
            $dbName = $tenant->data['tenancy_db_name'];

            if (empty($dbName)) continue;

            try {
                DB::statement("SET search_path TO {$dbName}");

                $docQuery = Document::whereNotNull('doc_key_clicksign');
                if ($startDate) $docQuery->whereDate('created_at', '>=', $startDate);
                if ($endDate)   $docQuery->whereDate('created_at', '<=', $endDate);

                $comDocQuery = ComissionDocument::whereNotNull('doc_key_clicksign_comission');
                if ($startDate) $comDocQuery->whereDate('created_at', '>=', $startDate);
                if ($endDate)   $comDocQuery->whereDate('created_at', '<=', $endDate);

                $totalDocs    = (clone $docQuery)->count()                           + (clone $comDocQuery)->count();
                $totalSigned  = (clone $docQuery)->where('status_sign', 1)->count()  + (clone $comDocQuery)->where('status_sign_comission', 1)->count();
                $totalPending = (clone $docQuery)->where('status_sign', 0)->count()  + (clone $comDocQuery)->where('status_sign_comission', 0)->count();
                $totalExpired = (clone $docQuery)->where('status_sign', 3)->count()  + (clone $comDocQuery)->where('status_sign_comission', 3)->count();

                if ($totalDocs === 0) continue;

                $reportData[] = [
                    'tenant_id'    => $tenant->id,
                    'tenant_city'  => $tenant->credentials->first()->city_name ?? $tenant->id,
                    'total_docs'   => $totalDocs,
                    'total_signed' => $totalSigned,
                    'total_pending' => $totalPending,
                    'total_expired' => $totalExpired,
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        usort($reportData, fn($a, $b) => $b['total_docs'] <=> $a['total_docs']);

        return $reportData;
    }

    public function clearCity(string $tenant_id): void
    {
        $tenant = Tenant::findOrFail($tenant_id);
        $dbName = $tenant->data['tenancy_db_name'];

        try {
            DB::statement("SET search_path TO {$dbName}");
            Clicksign::where('id', '!=', '0')->delete();
        } catch (\Exception $e) {
            throw new Exception('Erro ao deletar registros: ' . $e->getMessage());
        }
    }

    private function orderCities(array $cities): array
    {
        usort($cities, fn($a, $b) => $a['tenant_city'] <=> $b['tenant_city']);
        return $cities;
    }
}
