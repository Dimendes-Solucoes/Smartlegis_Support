<?php

namespace App\Libraries\LegalNorm;

class ProcessedDocument
{
    public function __construct(
        public readonly string $text,
        public readonly string $pdfPath,
        public readonly bool $wasConverted,
        public readonly bool $usedOcr,
    ) {}

    public function hasUsableText(): bool
    {
        return mb_strlen(trim($this->text)) >= DocumentProcessor::MIN_TEXT_LENGTH;
    }
}
