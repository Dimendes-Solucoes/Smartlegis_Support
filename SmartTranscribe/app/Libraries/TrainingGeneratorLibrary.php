<?php

namespace App\Libraries;

use App\Models\Resume;
use InvalidArgumentException;

class TrainingGeneratorLibrary
{
    protected string $base;
    protected ?string $input;

    public function __construct()
    {
        $this->base = "";
        $this->input = "";
    }

    public function generate(string $model, string $base, ?string $input = ""): array
    {
        $this->base = $base;
        $this->input = $input ?? "";

        $messages = match ($model) {
            Resume::MODEL_MINUTE => $this->trainingMinute(),
            Resume::MODEL_RESUME => $this->trainingResume(),
            Resume::MODEL_CUSTOM => $this->trainingCustom(),
            default => throw new InvalidArgumentException("Modelo inválido: {$model}")
        };

        return [
            "model" => env('OPENAI_API_MODEL'),
            "messages" => $messages,
            "temperature" => 1.0
        ];
    }

    private function trainingMinute(): array
    {
        $systemPrompt = "Você é um assistente de IA especialista na redação de atas de sessões legislativas de Câmaras Municipais.\n";
        $systemPrompt .= "Seu objetivo é redigir uma ata em estilo narrativo e formal, seguindo a ordem cronológica dos eventos da sessão, baseando-se no conteúdo fornecido.\n\n";
        $systemPrompt .= "REGRAS ESTRITAS DE FORMATAÇÃO E ESTILO:\n";
        $systemPrompt .= "1. **Formato Contínuo:** A ata deve ser um texto corrido, sem divisões, seções ou títulos em negrito como 'Resumo' ou 'Decisões'. A estrutura é um único bloco narrativo.\n";
        $systemPrompt .= "2. **Parágrafo de Abertura:** Comece sempre com um parágrafo que identifique a sessão, incluindo número, tipo (ordinária/extraordinária), local, data, hora e o nome da autoridade que presidiu os trabalhos. Sem inventar informações. Deixe lacunas se necessário.\n";
        $systemPrompt .= "3. **Ordem Cronológica:** Descreva as proposições, projetos, debates e votações na exata sequência em que ocorreram, conforme o texto base. Sem inventar narradores, se não estiver claro deixe em aberto.\n";
        $systemPrompt .= "4. **Linguagem Formal de Transição:** Utilize expressões formais para ligar os eventos, como 'Em continuidade', 'Ato contínuo', 'Na sequência', 'Dando prosseguimento', 'Encerradas as discussões'.\n";
        $systemPrompt .= "5. **Descrição de Cada Item:** Para cada proposição (requerimento ou projeto de lei), descreva seu número e autoria, explique seu objetivo, resuma brevemente a discussão (se houver) e declare o resultado da votação (ex: 'A proposição foi discutida e aprovada por todos os presentes.').\n";
        $systemPrompt .= "6. **Parágrafo de Encerramento:** Finalize a ata com uma frase que declare o encerramento da sessão, mencionando o nome do presidente, seguido da datação do documento (Local, Data).\n";
        $systemPrompt .= "7. **Formato de Saída:** O resultado final deve ser formatado como parágrafos HTML (`<p>...</p>`).\n";
        $systemPrompt .= "8. **Não gere informações falsas:** Seja fiel a tudo que for informado, sem criar informações que não foram passadas no texto base. Se necessário utilize [INFORMAÇÃO AUSENTE].\n";

        if (!empty($this->input)) {
            $systemPrompt .= "\n\nInstruções adicionais a serem consideradas: {$this->input}";
        }

        return [
            [
                "role" => "system",
                "content" => $systemPrompt
            ],
            [
                "role" => "user",
                "content" => "Com base nas regras definidas, gere uma ata formal do seguinte conteúdo:\n\n{$this->base}"
            ]
        ];
    }

    private function trainingResume(): array
    {
        $userPrompt = "Você é um assistente de IA focado em criar resumos concisos e informativos.\n";
        $userPrompt .= "Extraia os pontos-chave do texto e apresente-os de forma clara e direta.\n";
        $userPrompt .= "O resumo NÃO deve utilizar tópicos, listas ou enumerações, a menos que especificamente orientado nas instruções adicionais.\n";
        $userPrompt .= "O resultado final DEVE ser formatado estritamente como parágrafos HTML, onde cada parágrafo é envolto pela tag <p> e </p>.\n\n";

        if (!empty($this->input)) {
            $userPrompt .= "\n\nInstruções adicionais a serem consideradas: {$this->input}";
        }

        return [
            [
                "role" => "system",
                "content" => $userPrompt
            ],
            [
                "role" => "user",
                "content" => "Gere um resumo do seguinte conteúdo:\n\n{$this->base}"
            ]
        ];
    }

    private function trainingCustom(): array
    {
        $userPrompt = "Você é um assistente de IA flexível e prestativo para gerar textos. Siga as instruções do usuário da melhor maneira possível.\n";
        $userPrompt .= "NÃO gere tópicos, listas ou enumerações, a menos que especificamente orientado nas instruções adicionais.\n";
        $userPrompt .= "O resultado final DEVE ser formatado estritamente como parágrafos HTML, onde cada parágrafo é envolto pela tag <p> e </p>.\n\n";
        $userPrompt .= "\n\nInstruções adicionais a serem consideradas: {$this->input}";

        return [
            [
                "role" => "system",
                "content" => $userPrompt
            ],
            [
                "role" => "user",
                "content" => "Gere um texto do seguinte conteúdo:\n\n{$this->base}"
            ]
        ];
    }
}
