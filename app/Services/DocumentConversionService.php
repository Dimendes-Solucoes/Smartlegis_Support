<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Writer\HTML;
use Smalot\PdfParser\Parser as PdfParser;

class DocumentConversionService
{
    public function convertWordToHtml($file): string
    {
        try {
            $tempPath = $file->getRealPath();

            $command = sprintf(
                'node --input-type=module -e "' .
                    'import mammoth from \'mammoth\'; ' .
                    'mammoth.convertToHtml({path: \'%s\'})' .
                    '.then(r => console.log(r.value))' .
                    '.catch(() => process.exit(1));"',
                addslashes($tempPath)
            );

            $html = shell_exec($command);

            if (empty($html)) {
                throw new \Exception('Falha na conversão');
            }

            return $this->cleanHtml($html);
        } catch (\Exception $e) {
            Log::error('Erro na conversão Word: ' . $e->getMessage());
            throw new \Exception('Erro ao processar o arquivo Word.');
        }
    }

    public function convertWordSimple($phpWord): string
    {
        $html = '<div>';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $elementClass = get_class($element);

                // Processar parágrafos
                if (strpos($elementClass, 'TextRun') !== false || strpos($elementClass, 'Text') !== false) {
                    $text = method_exists($element, 'getText') ? $element->getText() : '';
                    if (!empty($text)) {
                        $html .= '<p>' . htmlspecialchars($text) . '</p>';
                    }
                }
            }
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * Converte arquivo PDF para HTML
     */
    public function convertPdfToHtml($file): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());

            // Extrair texto do PDF
            $text = $pdf->getText();

            if (empty($text)) {
                throw new \Exception('Não foi possível extrair texto do PDF');
            }

            // Converter texto simples para HTML com parágrafos
            $html = '<div class="pdf-content">';

            // Dividir em parágrafos (linhas em branco separam parágrafos)
            $paragraphs = preg_split('/\n\s*\n/', trim($text));

            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if (!empty($paragraph)) {
                    // Substituir quebras de linha simples por espaços
                    $paragraph = preg_replace('/\n/', ' ', $paragraph);
                    // Limpar espaços múltiplos
                    $paragraph = preg_replace('/\s+/', ' ', $paragraph);
                    $html .= '<p>' . htmlspecialchars($paragraph) . '</p>';
                }
            }

            $html .= '</div>';

            return $html;
        } catch (\Exception $e) {
            Log::error('Erro na conversão PDF: ' . $e->getMessage());
            throw new \Exception('Erro ao processar o arquivo PDF. Verifique se o arquivo não está protegido ou corrompido.');
        }
    }

    /**
     * Limpa e formata o HTML gerado
     */
    private function cleanHtml(string $html): string
    {
        // Remover tags desnecessárias
        $html = preg_replace('/<\?xml[^>]*>/', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<meta[^>]*>/', '', $html);

        // Extrair apenas o conteúdo do body se existir
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $html = $matches[1];
        }

        // Limpar atributos de estilo inline excessivos (mas manter formatação básica)
        $html = preg_replace('/\s*style="[^"]*"/', '', $html);

        // Remover classes geradas automaticamente
        $html = preg_replace('/\s*class="[^"]*"/', '', $html);

        // Remover atributos vazios
        $html = preg_replace('/\s+\w+=""/', '', $html);

        // Limpar espaços extras entre tags
        $html = preg_replace('/>\s+</', '><', $html);

        // Adicionar quebras de linha após tags de bloco para melhor legibilidade
        $html = preg_replace('/<\/(p|div|h[1-6]|ul|ol|li)>/', "</$1>\n", $html);

        $html = trim($html);

        return $html;
    }
}
