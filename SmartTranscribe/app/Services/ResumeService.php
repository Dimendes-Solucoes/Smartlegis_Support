<?php

namespace App\Services;

use App\Libraries\OpenAiLibrary;
use App\Models\Resume;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResumeService
{
    public function __construct(
        protected OpenAiLibrary $openAiLibrary
    ) {}

    public function list()
    {
        return Resume::all();
    }

    public function create(array $data)
    {
        try {
            $prompt = "Crie uma ata de reunião com as seguintes informações: \n" .
                "Data: {$data['date']}\n" .
                "Localização: {$data['localization']}\n" .
                "Participantes: {$data['participants']}\n" .
                "Assunto: {$data['subject']}\n" .
                "Conteúdo: {$data['input']}";

            $minute = $this->openAiLibrary->makeRequest($prompt);

            return Resume::create(['content' => $minute]);
        } catch (\Throwable $e) {
            dd($e);
            Log::error('Erro ao criar a resumo: ' . $e->getMessage());
            throw new \Exception('Erro ao criar resumo');
        }
    }

    public function delete(int $id)
    {
        $resume = Resume::findOrFail($id);

        if (!$resume) {
            throw new NotFoundHttpException('Resumo não encontrado');
        }

        $resume->delete();
    }
}
