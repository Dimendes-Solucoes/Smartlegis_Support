<?php

namespace App\Services;

use App\Libraries\OpenAiLibrary;
use App\Libraries\TrainingGeneratorLibrary;
use App\Models\Resume;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ResumeService
{
    public function __construct(
        protected OpenAiLibrary $openAiLibrary,
        protected TrainingGeneratorLibrary $trainingGeneratorLibrary
    ) {}

    public function list()
    {
        return Resume::all();
    }

    public function create(array $data)
    {
        try {
            $model = $data['model'];
            $base = $data['base'];
            $input = $data['input'] ?? "";

            $prompt = $this->trainingGeneratorLibrary->generate($model, $base, $input);
            $content = $this->openAiLibrary->makeRequest($prompt);

            return Resume::create([
                'client_id' => currentClient()->id,
                'content' => $content,
                'model' => $model
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar resumo: ' . $e->getMessage());
            throw new \Exception('Erro ao criar resumo');
        }
    }

    public function delete(int $id)
    {
        $resume = Resume::find($id);

        if (!$resume) {
            throw new NotFoundResourceException('Resumo não encontrado');
        }

        $resume->delete();
    }
}
