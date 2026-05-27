<?php

namespace App\Services;

use App\Libraries\LegalNorm\DocumentProcessor;
use App\Libraries\LegalNorm\OpenAiLegalNormExtractor;
use App\Libraries\StorageCustom;
use App\Models\Tenancy\LegalNorm;
use App\Models\Tenancy\NormSubject;
use App\Models\Tenancy\NormType;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;

class LegalNormService
{
    public function __construct(
        protected OpenAiLegalNormExtractor $extractor,
        protected DocumentProcessor $documentProcessor,
    ) {}

    public function list(
        ?string $search = null,
        ?int $year = null,
        ?int $normTypeId = null,
        ?int $normSubjectId = null,
    ): LengthAwarePaginator {
        $query = LegalNorm::with([
            'normType'    => fn($q) => $q->select('id', 'name', 'abbreviation'),
            'normSubject' => fn($q) => $q->select('id', 'name'),
        ])
            ->join('norm_types', 'legal_norms.norm_type_id', '=', 'norm_types.id')
            ->select('legal_norms.*')
            ->orderByRaw('EXTRACT(YEAR FROM legal_norms.publication_date) DESC')
            ->orderByRaw("CASE WHEN legal_norms.number ~ '^[0-9]+$' THEN CAST(legal_norms.number AS INTEGER) ELSE NULL END DESC NULLS LAST")
            ->orderBy('norm_types.abbreviation', 'ASC');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('legal_norms.object', 'like', "%{$search}%")
                    ->orWhere('legal_norms.number', 'like', "%{$search}%");
            });
        }

        if ($year) {
            $query->whereRaw('EXTRACT(YEAR FROM legal_norms.publication_date) = ?', [$year]);
        }

        if ($normTypeId) {
            $query->where('legal_norms.norm_type_id', $normTypeId);
        }

        if ($normSubjectId) {
            $query->where('legal_norms.norm_subject_id', $normSubjectId);
        }

        return $query->paginate(50)->withQueryString();
    }

    public function availableYears(): array
    {
        return LegalNorm::selectRaw('EXTRACT(YEAR FROM publication_date)::int AS year')
            ->whereNotNull('publication_date')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
    }

    public function update(int $id, array $data): LegalNorm
    {
        $norm = LegalNorm::findOrFail($id);
        $norm->update(array_filter($data, fn($value) => ! is_null($value)));

        return $norm->fresh(['normType', 'normSubject']);
    }

    public function reprocess(LegalNorm $norm): array
    {
        $relativePath = ltrim($norm->attachment, '/');
        $tempPath     = StorageCustom::download($relativePath);

        if (! $tempPath) {
            throw new RuntimeException('Arquivo não encontrado no armazenamento.');
        }

        try {
            $fakeFile = new UploadedFile(
                path: $tempPath,
                originalName: basename($relativePath),
                mimeType: 'application/pdf',
                error: UPLOAD_ERR_OK,
                test: true,
            );

            $processed = $this->documentProcessor->process($fakeFile);

            $normTypes    = NormType::select('id', 'name', 'abbreviation')->get()->toArray();
            $normSubjects = NormSubject::select('id', 'name')->get()->toArray();

            return $this->extractor->extract(
                pdfText: $processed->text,
                filename: basename($relativePath),
                normTypes: $normTypes,
                normSubjects: $normSubjects,
            );
        } finally {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }
    }
}
