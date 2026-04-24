<?php

namespace App\Services;

use App\Exceptions\ClickSignException;
use App\Models\Credential;
use App\Models\Tenant;
use App\Models\Tenancy\Author;
use App\Models\Tenancy\Clicksign;
use App\Models\Tenancy\ComissionDocument;
use App\Models\Tenancy\Document;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserCategory;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickSignService
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {}

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

    // ------------------------------------------------------------------
    // Ações públicas
    // ------------------------------------------------------------------

    public function resend(int $documentId): string
    {
        $document = Document::findOrFail($documentId);
        $usersId  = Author::where('document_id', $documentId)->pluck('user_id')->toArray();

        if (empty($usersId)) {
            throw new ClickSignException("Nenhum autor encontrado para o documento #{$documentId}.");
        }

        $response = $this->request('post', '/clicksign/resend', [
            'document_id' => $document->id,
            'users_id'    => $usersId,
        ]);

        return $response->json('message') ?? 'Documento reenviado para assinatura com sucesso!';
    }

    public function regenerateStamp(int $documentId): string
    {
        $document = Document::findOrFail($documentId);

        $response = $this->request('post', '/clicksign/regenerate-stamp', [
            'document_id' => $document->id,
        ]);

        return $response->json('message') ?? 'Carimbo regenerado com sucesso!';
    }

    public function refreshStatus(int $documentId): string
    {
        $document = Document::findOrFail($documentId);

        $response = $this->request('post', '/clicksign/refresh-status-sign', [
            'document_id' => $document->id,
        ]);

        return $response->json('message') ?? 'Status de assinatura recalculado com sucesso!';
    }

    // ------------------------------------------------------------------
    // HTTP
    // ------------------------------------------------------------------

    private function request(string $method, string $endpoint, array $payload = []): Response
    {
        try {
            $response = $this->buildClient()->{$method}($endpoint, $payload);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ClickSign: falha de conexão', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            throw new ClickSignException(
                'Não foi possível conectar ao serviço ClickSign. Tente novamente em instantes.'
            );
        }

        Log::info('ClickSign: resposta recebida', [
            'endpoint' => $endpoint,
            'status'   => $response->status(),
            'body'     => $response->json(),
        ]);

        if ($response->successful()) {
            return $response;
        }

        $this->handleError($response, $endpoint);
    }

    private function buildClient(): PendingRequest
    {
        ['credential' => $credential, 'dbName' => $dbName] = $this->resolveCredential();

        $token = $this->resolveToken($credential, $dbName);

        return Http::withHeaders(['X-Service-Secret' => config('app.service_secret')])
            ->withToken($token)
            ->baseUrl("https://{$credential->host}/api")
            ->acceptJson()
            ->timeout(30)
            ->connectTimeout(10);
    }

    // ------------------------------------------------------------------
    // Credencial e token
    // ------------------------------------------------------------------

    private function resolveCredential(): array
    {
        try {
            $tenantId = $this->tenantService->getCurrentTenantId();
            $tenant   = $this->tenantService->getCurrentTenant();
            $dbName   = $tenant->data['tenancy_db_name'] ?? null;

            DB::statement('SET search_path TO public');
            $credential = Credential::where('tenant_id', $tenantId)->firstOrFail();

            if ($dbName) {
                DB::statement("SET search_path TO {$dbName}");
            }

            return compact('credential', 'dbName');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            throw new ClickSignException('Credencial ClickSign não encontrada para este tenant.');
        } catch (\Throwable $e) {
            Log::error('ClickSign: erro ao resolver credencial', ['error' => $e->getMessage()]);
            throw new ClickSignException('Erro ao inicializar o serviço ClickSign.');
        }
    }

    private function resolveToken(Credential $credential, ?string $dbName): string
    {
        if ($credential->service_token) {
            return $credential->service_token;
        }

        $integrationUser = $this->resolveIntegrationUser($dbName);

        $token = $integrationUser->createToken('service-clicksign-' . $credential->tenant_id)->plainTextToken;

        DB::statement('SET search_path TO public');
        $credential->update(['service_token' => $token]);

        if ($dbName) {
            DB::statement("SET search_path TO {$dbName}");
        }

        return $token;
    }

    private function resolveIntegrationUser(?string $dbName): User
    {
        if ($dbName) {
            DB::statement("SET search_path TO {$dbName}");
        }

        $user = User::where('user_category_id', UserCategory::INTEGRACAO)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            throw new ClickSignException(
                "Usuário de integração (categoria INTEGRACAO) não encontrado no tenant."
            );
        }

        return $user;
    }

    private function invalidateToken(string $tenantId): void
    {
        try {
            DB::statement('SET search_path TO public');
            $credential = Credential::where('tenant_id', $tenantId)->first();
            $credential?->update(['service_token' => null]);

            Log::info('ClickSign: service_token invalidado por 401', compact('tenantId'));
        } catch (\Throwable $e) {
            Log::warning('ClickSign: falha ao limpar token após 401', ['error' => $e->getMessage()]);
        }
    }

    // ------------------------------------------------------------------
    // Tratamento de erros
    // ------------------------------------------------------------------

    private function handleError(Response $response, string $endpoint): never
    {
        $status = $response->status();

        Log::warning('ClickSign: erro na requisição', [
            'endpoint' => $endpoint,
            'status'   => $status,
            'body'     => $response->json(),
        ]);

        $message = match (true) {
            $status === 401 => $this->handleUnauthorized(),
            $status === 403 => 'Sem permissão para executar esta ação no ClickSign.',
            $status === 404 => "Rota não encontrada no serviço ClickSign: {$endpoint}",
            $status === 422 => $this->extractValidationMessage($response),
            in_array($status, [408, 504]) => 'O serviço ClickSign demorou para responder. Tente novamente.',
            in_array($status, [500, 502, 503]) => 'Erro interno no serviço ClickSign. Tente novamente em instantes.',
            default => $response->json('message') ?? "Erro inesperado ao comunicar com o ClickSign (HTTP {$status}).",
        };

        throw new ClickSignException($message, $status);
    }

    private function handleUnauthorized(): string
    {
        $tenant_id = $this->tenantService->getCurrentTenantId();
        $this->invalidateToken($tenant_id);

        return 'Token de autenticação inválido ou expirado. A sessão será renovada na próxima tentativa.';
    }

    private function extractValidationMessage(Response $response): string
    {
        $errors = $response->json('errors');

        if (is_array($errors)) {
            return 'Dados inválidos: ' . collect($errors)->flatten()->implode(' | ');
        }

        return $response->json('message') ?? 'Dados inválidos enviados ao ClickSign.';
    }
}
