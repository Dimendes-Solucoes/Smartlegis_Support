<?php

namespace App\Services;

use App\Libraries\LegalNorm\DocumentProcessor;
use App\Libraries\LegalNorm\OpenAiLegalNormExtractor;
use App\Libraries\StorageCustom;
use App\Models\Tenancy\LegalNorm;
use App\Models\Tenancy\NormSubject;
use App\Models\Tenancy\NormType;
use App\Models\Tenancy\TempLegalNorm;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TempLegalNormService
{
    public function __construct(
        protected OpenAiLegalNormExtractor $extractor,
        protected DocumentProcessor $documentProcessor,
    ) {}

    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        return TempLegalNorm::pending()
            ->where('created_by', Auth::id())
            ->with([
                'normType'    => fn ($q) => $q->select('id', 'name', 'abbreviation'),
                'normSubject' => fn ($q) => $q->select('id', 'name'),
            ])
            ->latest()
            ->get();
    }

    public function uploadAndExtract(UploadedFile $file): TempLegalNorm
    {
        $processed = $this->documentProcessor->process($file);

        $fileToUpload = $processed->wasConverted
            ? $this->wrapPdfAsUploadedFile($processed->pdfPath, $file->getClientOriginalName())
            : $file;

        $upload = StorageCustom::upload($fileToUpload, 'legal_norm_attachments');

        if (!$upload?->path) {
            throw new \RuntimeException('Não foi possível fazer upload do arquivo.');
        }

        $normTypes = NormType::select('id', 'name', 'abbreviation')->get()->toArray();
        $normSubjects = NormSubject::select('id', 'name')->get()->toArray();

        $extracted = $this->extractor->extract(
            pdfText: $processed->text,
            filename: $file->getClientOriginalName(),
            normTypes: $normTypes,
            normSubjects: $normSubjects,
        );

        return TempLegalNorm::create([
            'object'            => $extracted['object']           ?? null,
            'number'            => $extracted['number']           ?? null,
            'attachment'        => "/{$upload->path}",
            'publication_date'  => $extracted['publication_date'] ?? null,
            'norm_type_id'      => $extracted['norm_type_id']     ?? null,
            'norm_subject_id'   => $extracted['norm_subject_id']  ?? null,
            'is_active'         => true,
            'original_filename' => $file->getClientOriginalName(),
            'status'            => 'pending',
            'created_by'        => Auth::id(),
            'extraction_meta'   => [
                'confidence'      => $extracted['confidence']      ?? [],
                'pdf_text_length' => mb_strlen($processed->text),
                'was_converted'   => $processed->wasConverted,
                'used_ocr'        => $processed->usedOcr,
            ],
        ]);
    }

    public function update(int $id, array $data): TempLegalNorm
    {
        $temp = $this->findPending($id);

        $temp->update(array_filter($data, fn ($value) => ! is_null($value)));

        return $temp->fresh(['normType', 'normSubject']);
    }

    public function confirm(int $id): LegalNorm
    {
        $temp = $this->findPending($id);

        $data = [
            'object'           => $temp->object,
            'number'           => $temp->number,
            'attachment'       => $temp->attachment,
            'publication_date' => $temp->publication_date?->format('Y-m-d'),
            'norm_type_id'     => $temp->norm_type_id,
            'norm_subject_id'  => $temp->norm_subject_id,
            'is_active'        => $temp->is_active,
        ];

        DB::beginTransaction();

        try {
            $norm = LegalNorm::create($data);
            $temp->update(['status' => 'confirmed']);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TempLegalNormService::confirm falhou', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }

        return $norm;
    }

    public function confirmBatch(array $ids = []): array
    {
        $query = TempLegalNorm::pending()->where('created_by', Auth::id());

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $temps     = $query->get();
        $confirmed = [];
        $errors    = [];

        foreach ($temps as $temp) {
            try {
                $confirmed[] = $this->confirm($temp->id);
            } catch (\Throwable $e) {
                $errors[] = [
                    'id'    => $temp->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return compact('confirmed', 'errors');
    }

    public function discard(int $id): void
    {
        $this->findPending($id)->update(['status' => 'discarded']);
    }

    public function discardBatch(array $ids = []): int
    {
        $query = TempLegalNorm::pending()->where('created_by', Auth::id());

        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->update(['status' => 'discarded']);
    }

    private function findPending(int $id): TempLegalNorm
    {
        return TempLegalNorm::where('id', $id)
            ->where('status', 'pending')
            ->where('created_by', Auth::id())
            ->firstOrFail();
    }

    private function wrapPdfAsUploadedFile(string $pdfPath, string $originalName): UploadedFile
    {
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

        return new UploadedFile(
            path: $pdfPath,
            originalName: $nameWithoutExt . '.pdf',
            mimeType: 'application/pdf',
            error: UPLOAD_ERR_OK,
            test: true
        );
    }
}
