<?php

namespace App\Services;

use App\Libraries\FileUploader;
use App\Models\Credential;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketMessage;
use App\Models\Helpdesk\TicketStatus;
use App\Models\Helpdesk\TicketType;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketService
{
    public function list(array $filter = []): LengthAwarePaginator
    {
        $query = $this->buildQuery($filter);

        return $query->orderBy('id', 'desc')->paginate($filter['per_page'] ?? 15);
    }

    public function findByCode(string $code): ?Ticket
    {
        $query = $this->buildQuery(['code' => $code]);

        return $query->first();
    }

    public function prepareForCreate()
    {
        $types = TicketType::orderBy('title')->get();
        $status = TicketStatus::orderBy('id')->get();
        $credentials = Credential::orderBy('city_name')->get();

        return [
            'ticket_types' => $types,
            'ticket_status' => $status,
            'credentials' => $credentials
        ];
    }

    public function create(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();

            $ticket = Ticket::create([
                'ticket_type_id' => $data['ticket_type_id'],
                'ticket_status_id' => $data['ticket_status_id'],
                'author_id' => $user->id,
                'code' => Str::uuid(),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            if (!empty($data['credential_ids'])) {
                $ticket->credentials()->attach($data['credential_ids']);
            }

            if (!empty($data['attachments'])) {
                $this->handleAttachmentUpload($ticket, $data['attachments']);
            }

            return $ticket;
        });
    }

    public function updateByCode(string $code, array $data): Ticket
    {
        $ticket = Ticket::where('code', $code)->first();

        if (!$ticket) {
            throw new Exception("Ticket não encontrado");
        }

        return DB::transaction(function () use ($ticket, $data) {
            $ticketData = Arr::only($data, [
                'ticket_type_id',
                'ticket_status_id'
            ]);

            $ticket->update($ticketData);

            if (isset($data['credential_ids'])) {
                $ticket->credentials()->sync($data['credential_ids']);
            }

            return $ticket;
        });
    }

    public function addAttachments(int $id, array $data): void
    {
        $ticket = Ticket::findOrFail($id);

        if (empty($data['attachments'])) {
            return;
        }

        $this->handleAttachmentUpload($ticket, $data['attachments']);
    }

    public function removeAttachments(int $id, array $data): void
    {
        if (empty($data['attachment_ids'])) {
            return;
        }

        $attachments = TicketAttachment::where('ticket_id', $id)
            ->whereIn('id', $data['attachment_ids'])
            ->get();

        foreach ($attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }
    }

    public function sendMessage(int $id, array $data): TicketMessage
    {
        $user = Auth::user();

        return TicketMessage::create([
            'ticket_id' => $id,
            'author_id' => $user->id,
            'author_name' => $user->name,
            'content' => $data['content'],
            'credential_id' => $user->credential_id,
        ]);
    }

    public function removeMessage(int $id, array $data): void
    {
        if (empty($data['message_ids'])) {
            return;
        }

        TicketMessage::where('ticket_id', $id)
            ->whereIn('id', $data['message_ids'])
            ->delete();
    }

    private function buildQuery(array $filter = []): Builder
    {
        $query = Ticket::query()
            ->with([
                'type',
                'status',
                'author',
                'credentials',
                'messages.author',
                'messages.attachments',
                'attachments'
            ]);

        if (isset($filter['start_date']) || isset($filter['end_date'])) {
            $startDate = $filter['start_date'] ?? Carbon::now()->startOfMonth()->toDateString();
            $endDate = $filter['end_date'] ?? Carbon::now()->endOfMonth()->toDateString();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $query->when(
            $filter['ticket_type_id'] ?? null,
            fn($q, $typeId) => $q->where('ticket_type_id', $typeId)
        );

        $query->when(
            $filter['ticket_status_id'] ?? null,
            fn($q, $statusId) => $q->where('ticket_status_id', $statusId)
        );

        $query->when(
            $filter['author_id'] ?? null,
            fn($q, $authorId) => $q->where('author_id', $authorId)
        );

        $query->when(
            $filter['credential_id'] ?? null,
            fn($q, $credentialId) => $q->whereHas('credentials', fn($q) => $q->where('credential_id', $credentialId))
        );

        $query->when($filter['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('code', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        });

        return $query;
    }

    private function handleAttachmentUpload(Ticket $ticket, array $files): void
    {
        $user = Auth::user();

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadData = FileUploader::handleUpload($file, "tickets/{$ticket->id}");

                if (!empty($uploadData)) {
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $user->id,
                        'file_name' => $uploadData['file_name'],
                        'file_path' => $uploadData['file_path'],
                    ]);
                }
            }
        }
    }
}
