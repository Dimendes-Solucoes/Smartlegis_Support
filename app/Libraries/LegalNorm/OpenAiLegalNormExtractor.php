<?php

namespace App\Libraries\LegalNorm;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiLegalNormExtractor
{
    protected string $url;
    protected string $token;
    protected string $model;

    public function __construct()
    {
        $this->url   = config('openai.url', '');
        $this->token = config('openai.key', '');
        $this->model = config('openai.model', 'gpt-4o-mini');

        if (! $this->url || ! $this->token) {
            throw new \RuntimeException(
                'OPENAI_API_URL e OPENAI_API_KEY nao estao configuradas.'
            );
        }
    }

    public function extract(string $pdfText, string $filename, array $normTypes, array $normSubjects): array
    {
        $normTypesJson    = json_encode($normTypes,    JSON_UNESCAPED_UNICODE);
        $normSubjectsJson = json_encode($normSubjects, JSON_UNESCAPED_UNICODE);

        $systemPrompt = <<<PROMPT
            Você é um extrator especializado em normas jurídicas brasileiras (leis municipais, decretos, portarias, resoluções, etc).

            A partir do texto de um documento, extraia as seguintes informações e retorne APENAS um JSON válido, sem explicações, sem markdown.

            Campos obrigatórios:
            - "object": string — descrição resumida do conteúdo/ementa da norma (máx 120 chars). nao adicione aqui o numero e ano do documento.
            - "number": string — número identificador da norma (ex: "123", "45"). Apenas números. Se não tiver retorne vazio.
            - "publication_date": string no formato "Y-m-d" — data de publicação. Se não houver data, use "YYYY-01-01" com o ano identificado no documento. Se não encontrar ano, use o ano atual.
            - "norm_type_id": integer — ID do tipo mais próximo entre os disponíveis abaixo. Em lei de preferencia por lei sancionada, se houver.
            - "norm_subject_id": integer — ID do assunto mais próximo entre os disponíveis abaixo
            - "confidence": object — {"object": 0-1, "number": 0-1, "publication_date": 0-1, "norm_type_id": 0-1, "norm_subject_id": 0-1}

            Tipos disponíveis: {$normTypesJson}
            Assuntos disponíveis: {$normSubjectsJson}

            Retorne SOMENTE o JSON, sem nenhum texto adicional.
            PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => "Nome do arquivo: {$filename}\n\nConteúdo:\n{$pdfText}"],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->timeout(60)->post($this->url, [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.1,
        ]);

        if ($response->failed()) {
            Log::error('OpenAiLegalNormExtractor: erro na API', ['body' => $response->body()]);
            throw new Exception('Erro ao consultar OpenAI: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');

        if (! $content) {
            throw new Exception('OpenAI retornou resposta vazia.');
        }

        $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
        $content = preg_replace('/\s*```$/', '', $content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('OpenAiLegalNormExtractor: JSON inválido', ['content' => $content]);
            throw new Exception('Resposta da OpenAI não é um JSON válido.');
        }

        Log::info('OpenAiLegalNormExtractor: extração concluída', ['filename' => $filename, 'result' => $decoded]);

        return $decoded;
    }
}
