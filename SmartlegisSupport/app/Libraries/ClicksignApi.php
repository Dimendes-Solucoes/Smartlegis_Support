<?php

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClicksignApi
{
    private static string $host;
    private static string $token;
    private static PendingRequest $httpClient;

    public const SIGN_AS_SIGN = 'sign';
    public const AUTH_EMAIL = 'email';
    public const DELIVERY_EMAIL = 'email';

    public static function boot(): void
    {
        self::$host = env('CLICKSIGN_HOST');
        self::$token = env('CLICKSIGN_TOKEN');

        self::$httpClient = Http::baseUrl("https://" . self::$host . ".clicksign.com/api/v1/")
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }

    public static function sendDocument(string $fileName, string $attachmentPath, int $deadlineInMonths = 1): ?array
    {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $pdfContent = StorageCustom::get($attachmentPath);

            if (empty($pdfContent)) {
                Log::error("ClicksignApi: Conteúdo do PDF não encontrado para o caminho: " . $attachmentPath);
                return null;
            }

            $base64Content = base64_encode($pdfContent);
            $deadline = Carbon::now()->addMonths($deadlineInMonths)->format('Y-m-d\TH:i:sP');

            $response = self::$httpClient->post("documents?access_token=" . self::$token, [
                'document' => [
                    'path' => Str::start($fileName, '/'),
                    'content_base64' => 'data:application/pdf;base64,' . $base64Content,
                    'deadline_at' => $deadline,
                    'auto_close' => true,
                    'locale' => 'pt-BR',
                    'sequence_enabled' => false,
                    'block_after_refusal' => true,
                ]
            ]);

            $response->throw();

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao enviar documento. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao enviar documento: " . $e->getMessage());
        }

        return null;
    }

    public static function cancelDocument(string $docKey): ?array
    {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $response = self::$httpClient->patch("documents/{$docKey}/cancel?access_token=" . self::$token);
            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao cancelar documento. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao cancelar documento: " . $e->getMessage());
        }

        return null;
    }

    public static function deleteDocument(string $docKey): ?array
    {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $response = self::$httpClient->delete("documents/{$docKey}?access_token=" . self::$token);
            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao deletar documento. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao deletar documento: " . $e->getMessage());
        }

        return null;
    }

    public static function addSigner(string $docKey, string $signerKey, string $userName, string $message = '', string $signAs = self::SIGN_AS_SIGN): ?array
    {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $response = self::$httpClient->post("lists?access_token=" . self::$token, [
                'list' => [
                    'document_key' => $docKey,
                    'signer_key' => $signerKey,
                    'name' => $userName,
                    'sign_as' => $signAs,
                    'refusable' => true,
                    'group' => 0,
                    'message' => $message ?: "Prezado(a) {$userName}, assine o documento.",
                ]
            ]);

            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao adicionar signatário. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao adicionar signatário: " . $e->getMessage());
        }

        return null;
    }

    public static function removeSigner(string $listKey): ?array
    {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $response = self::$httpClient->delete("lists/{$listKey}?access_token=" . self::$token);
            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao remover signatário. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao remover signatário: " . $e->getMessage());
        }

        return null;
    }

    public static function createBatch(string $signerKey, array $documentKeys): ?array
    {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $response = self::$httpClient->post("batches?access_token=" . self::$token, [
                'batch' => [
                    'signer_key' => $signerKey,
                    'document_keys' => $documentKeys,
                    'summary' => true,
                ]
            ]);

            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao criar lote. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao criar lote: " . $e->getMessage());
        }

        return null;
    }

    public static function createSigner(
        string $userEmail,
        string $userName,
        array $auths = [self::AUTH_EMAIL],
        string $delivery = self::DELIVERY_EMAIL
    ): ?array {
        if (!isset(self::$httpClient)) {
            self::boot();
        }

        try {
            $response = self::$httpClient->post("signers?access_token=" . self::$token, [
                'signer' => [
                    'email' => $userEmail,
                    'auths' => $auths,
                    'delivery' => $delivery,
                    'name' => $userName,
                    'has_documentation' => false,
                    'selfie_enabled' => false,
                    'handwritten_enabled' => false,
                    'official_document_enabled' => false,
                    'liveness_enabled' => false,
                    'facial_biometrics_enabled' => false,
                ],
            ]);

            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("ClicksignApi: Erro ao criar signatário. Status: {$e->response->status()}, Erro: " . $e->getMessage(), ['response' => $e->response->json()]);
        } catch (\Throwable $e) {
            Log::error("ClicksignApi: Erro inesperado ao criar signatário: " . $e->getMessage());
        }

        return null;
    }
}
