<?php

namespace App\Libraries\LegalNorm;

readonly class ProcessedDocument
{
    public function __construct(
        public string $text,
        public string $pdfPath,
        public bool $wasConverted,
        public bool $usedOcr,
    ) {}

    public function hasUsableText(): bool
    {
        return mb_strlen(trim($this->text)) >= DocumentProcessor::MIN_TEXT_LENGTH;
    }
}
