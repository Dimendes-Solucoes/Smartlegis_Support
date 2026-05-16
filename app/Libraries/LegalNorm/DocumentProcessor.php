<?php

namespace App\Libraries\LegalNorm;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;

class DocumentProcessor
{
    public const MIN_TEXT_LENGTH = 100;
    public const MAX_TEXT_LENGTH = 12_000;

    private const OCR_DPI        = 150;
    private const OCR_LANG       = 'por';
    private const WORD_EXTENSIONS = ['doc', 'docx'];
    private const MAX_PAGES      = 2;

    public function process(UploadedFile $file): ProcessedDocument
    {
        $ext          = strtolower($file->getClientOriginalExtension());
        $wasConverted = in_array($ext, self::WORD_EXTENSIONS, true);

        $pdfPath = $wasConverted
            ? $this->convertWordToPdf($file->getRealPath())
            : $file->getRealPath();

        [$text, $usedOcr] = $this->extractText($pdfPath, $file->getClientOriginalName());

        return new ProcessedDocument(
            text: mb_substr(trim($text), 0, self::MAX_TEXT_LENGTH),
            pdfPath: $pdfPath,
            wasConverted: $wasConverted,
            usedOcr: $usedOcr,
        );
    }

    private function convertWordToPdf(string $sourcePath): string
    {
        $outDir  = sys_get_temp_dir();
        $outFile = $outDir . '/' . pathinfo($sourcePath, PATHINFO_FILENAME) . '.pdf';

        if (file_exists($outFile)) {
            unlink($outFile);
        }

        $cmd = sprintf(
            'HOME=/tmp soffice --headless --norestore --nofirststartwizard --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg($outDir),
            escapeshellarg($sourcePath),
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || ! file_exists($outFile)) {
            Log::error('DocumentProcessor: falha na conversão Word→PDF', [
                'source' => $sourcePath,
                'output' => implode("\n", $output),
            ]);
            throw new RuntimeException(
                'Não foi possível converter o arquivo Word para PDF. ' .
                'Verifique se o LibreOffice está instalado no servidor.'
            );
        }

        return $outFile;
    }

    private function extractText(string $pdfPath, string $originalName): array
    {
        $text = $this->extractTextNative($pdfPath, $originalName);

        if ($this->looksScanned($text)) {
            Log::info('DocumentProcessor: PDF parece escaneado, iniciando OCR', [
                'file'         => $originalName,
                'native_chars' => mb_strlen($text),
            ]);

            $ocrText = $this->extractTextWithOcr($pdfPath, $originalName);

            return [$ocrText, true];
        }

        return [$text, false];
    }

    private function extractTextNative(string $pdfPath, string $originalName): string
    {
        try {
            $pages = (new PdfParser())->parseFile($pdfPath)->getPages();

            return trim(implode("\n\n", array_map(
                fn ($page) => $page->getText(),
                array_slice($pages, 0, self::MAX_PAGES),
            )));
        } catch (\Throwable $e) {
            Log::warning('DocumentProcessor: falha na extração nativa', [
                'file'  => $originalName,
                'error' => $e->getMessage(),
            ]);

            return '';
        }
    }

    private function looksScanned(string $text): bool
    {
        return mb_strlen(trim($text)) < self::MIN_TEXT_LENGTH;
    }

    private function extractTextWithOcr(string $pdfPath, string $originalName): string
    {
        $tmpDir = sys_get_temp_dir() . '/ocr_' . uniqid('', true);

        if (! mkdir($tmpDir, 0700, true)) {
            throw new RuntimeException("Não foi possível criar diretório temporário: {$tmpDir}");
        }

        try {
            $this->rasterizePdf($pdfPath, $tmpDir, $originalName);

            return $this->runTesseractOnDir($tmpDir, $originalName);
        } finally {
            $this->cleanupDir($tmpDir);
        }
    }

    private function rasterizePdf(string $pdfPath, string $tmpDir, string $originalName): void
    {
        $outputPattern = $tmpDir . '/page-%03d.png';

        $cmd = sprintf(
            'gs -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pnggray -r%d -dFirstPage=1 -dLastPage=%d -sOutputFile=%s %s 2>&1',
            self::OCR_DPI,
            self::MAX_PAGES,
            escapeshellarg($outputPattern),
            escapeshellarg($pdfPath),
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            Log::error('DocumentProcessor: Ghostscript falhou', [
                'file'   => $originalName,
                'output' => implode("\n", $output),
            ]);
            throw new RuntimeException(
                'Ghostscript falhou ao rasterizar o PDF. ' .
                'Verifique se o Ghostscript está instalado no servidor.'
            );
        }
    }

    private function runTesseractOnDir(string $tmpDir, string $originalName): string
    {
        $images = glob($tmpDir . '/page-*.png');

        if (empty($images)) {
            Log::warning('DocumentProcessor: nenhuma imagem gerada pelo Ghostscript', [
                'file' => $originalName,
            ]);

            return '';
        }

        sort($images);

        $texts = [];

        foreach ($images as $image) {
            $outputBase = $tmpDir . '/ocr_' . pathinfo($image, PATHINFO_FILENAME);

            $cmd = sprintf(
                'tesseract %s %s -l %s --psm 6 --oem 1 quiet 2>&1',
                escapeshellarg($image),
                escapeshellarg($outputBase),
                self::OCR_LANG,
            );

            exec($cmd, $out, $exitCode);

            $txtFile = $outputBase . '.txt';

            if ($exitCode === 0 && file_exists($txtFile)) {
                $pageText = trim(file_get_contents($txtFile));

                if ($pageText !== '') {
                    $texts[] = $pageText;
                }
            } else {
                Log::warning('DocumentProcessor: Tesseract falhou em uma página', [
                    'image'  => basename($image),
                    'output' => implode("\n", $out),
                ]);
            }
        }

        Log::info('DocumentProcessor: OCR concluído', [
            'file'        => $originalName,
            'pages'       => count($images),
            'pages_ok'    => count($texts),
            'total_chars' => array_sum(array_map('mb_strlen', $texts)),
        ]);

        return implode("\n\n", $texts);
    }

    private function cleanupDir(string $dir): void
    {
        foreach (glob($dir . '/*') as $file) {
            @unlink($file);
        }
        @rmdir($dir);
    }
}
