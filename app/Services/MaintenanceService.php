<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserCategory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MaintenanceService
{
    private ?Credential $cachedCredential = null;
    private ?string $cachedDbName = null;

    public function __construct(
        private readonly TenantService $tenantService,
    ) {}

    public function refreshAll(): void
    {
        $this->request('post', '/tools/refresh-all');
    }

    public function refreshTv(): void
    {
        $this->request('post', '/tools/refresh-tv');
    }

    private function request(string $method, string $endpoint, array $payload = []): void
    {
        try {
            $response = $this->buildClient()->{$method}($endpoint, $payload);

            if ($response->status() === 401) {
                $this->cachedCredential->service_token = null;
                $response = $this->buildClient()->{$method}($endpoint, $payload);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Maintenance: falha de conexão', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);

            throw new RuntimeException('Não foi possível conectar ao serviço. Tente novamente em instantes.');
        }

        if (!$response->successful()) {
            Log::warning('Maintenance: erro na requisição', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->json(),
            ]);

            throw new RuntimeException(
                $response->json('message') ?? "Erro ao executar a manutenção (HTTP {$response->status()})."
            );
        }
    }

    private function buildClient(): PendingRequest
    {
        if (!$this->cachedCredential) {
            ['credential' => $this->cachedCredential, 'dbName' => $this->cachedDbName] = $this->resolveCredential();
        }

        $token = $this->resolveToken($this->cachedCredential, $this->cachedDbName);

        return Http::withHeaders(['X-Service-Secret' => config('app.service_secret')])
            ->withToken($token)
            ->baseUrl("https://{$this->cachedCredential->host}/api")
            ->acceptJson()
            ->timeout(30)
            ->connectTimeout(10);
    }

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
            throw new RuntimeException('Credencial não encontrada para este tenant.');
        } catch (\Throwable $e) {
            Log::error('Maintenance: erro ao resolver credencial', ['error' => $e->getMessage()]);
            throw new RuntimeException('Erro ao inicializar o serviço de manutenção.');
        }
    }

    private function resolveToken(Credential $credential, ?string $dbName): string
    {
        if ($credential->service_token) {
            return $credential->service_token;
        }

        if ($dbName) {
            DB::statement("SET search_path TO {$dbName}");
        }

        $user = User::where('user_category_id', UserCategory::INTEGRACAO)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            throw new RuntimeException('Usuário de integração não encontrado no tenant.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        DB::statement('SET search_path TO public');
        $credential->update(['service_token' => $token]);

        if ($dbName) {
            DB::statement("SET search_path TO {$dbName}");
        }

        return $token;
    }
}
