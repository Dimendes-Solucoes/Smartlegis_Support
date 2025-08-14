<?php

namespace App\Services;

use App\Models\Tenancy\Clicksign;
use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\DB;

class ClicksignService
{
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
                    'tenant_id' => $tenant->id,
                    'tenant_city' => $tenant->credentials->first()->city_name ?? $tenant->id,
                    'quantity' => Clicksign::count()
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        $allClicksignData = $this->orderCities($allClicksignData);

        return $allClicksignData;
    }

    public function clearCity(string $tenant_id)
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

    private function orderCities($cities)
    {
        usort($cities, fn($a, $b) => $a['tenant_city'] <=> $b['tenant_city']);
        return $cities;
    }
}
