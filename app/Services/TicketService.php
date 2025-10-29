<?php

namespace App\Services;

use App\Libraries\FileUploader;
use App\Libraries\StorageCustom;
use App\Models\Credential;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketMessage;
use App\Models\Helpdesk\TicketStatus;
use App\Models\Helpdesk\TicketType;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketService
{
    public function listForIndex(array $filter = []): array
    {
        $query = $this->buildQuery($filter);

        $tickets = $query
            ->with(['status', 'type', 'author'])
            ->withCount(['messages', 'attachments'])
            ->orderBy('id', 'desc')
            ->paginate($filter['per_page'] ?? 15);

        $ticketTypes = TicketType::orderBy('title')->get();
        $ticketStatuses = TicketStatus::orderBy('id')->get();
        $authors = User::orderBy('name')->get();

        return [
            'tickets' => $tickets,
            'ticketTypes' => $ticketTypes,
            'ticketStatuses' => $ticketStatuses,
            'authors' => $authors,
        ];
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

    public function addAttachmentsByCode(string $code, array $data): void
    {
        $ticket = Ticket::where('code', $code)->first();

        if (!$ticket) {
            throw new Exception("Ticket não encontrado");
        }

        if (empty($data['attachments'])) {
            return;
        }

        $this->handleAttachmentUpload($ticket, $data['attachments']);
    }

    public function removeAttachmentsByCode(string $code, int $attachment_id): void
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::where('code', $code)->first();

            if (!$ticket) {
                throw new ModelNotFoundException("Ticket não encontrado.");
            }

            $attachment = TicketAttachment::where('ticket_id', $ticket->id)
                ->where('id', $attachment_id)
                ->first();

            if (!$attachment) {
                throw new ModelNotFoundException("Anexo não encontrado.");
            }

            if ($attachment->user_id !== auth()->id()) {
                throw new Exception("Acesso negado. Você não é o proprietário deste ticket.");
            }

            $filePath = $attachment->file_path;

            if ($filePath) {
                StorageCustom::delete($filePath);
            }

            $attachment->delete();

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage() ?? "Não foi possível remover o anexo. Tente novamente.");
        }
    }

    public function sendMessageByCode(string $code, array $data): TicketMessage
    {
        $ticket = Ticket::where('code', $code)->first();

        if (!$ticket) {
            throw new Exception("Ticket não encontrado");
        }

        $user = Auth::user();

        return TicketMessage::create([
            'ticket_id' => $ticket->id,
            'author_id' => $user->id,
            'content' => $data['content']
        ]);
    }

    public function removeMessageByCode(string $code, int $message_id): void
    {
        $ticket = Ticket::where('code', $code)->first();

        if (!$ticket) {
            throw new Exception("Ticket não encontrado.");
        }

        $message = TicketMessage::where('ticket_id', $ticket->id)
            ->where('id', $message_id)
            ->first();

        if ($message->author_id !== auth()->id()) {
            throw new Exception("Acesso negado. Você não é o proprietário deste ticket.");
        }

        $message->delete();
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
                'attachments.user'
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
            $filter['code'] ?? null,
            fn($q, $code) => $q->where('code', $code)
        );

        $query->when(
            $filter['credential_id'] ?? null,
            fn($q, $credentialId) => $q->whereHas('credentials', fn($q) => $q->where('credential_id', $credentialId))
        );

        $query->when($filter['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('code', 'ilike', "%{$search}%");
            });
        });

        return $query;
    }

    private function handleAttachmentUpload(Ticket $ticket, array $files): void
    {
        $user = Auth::user();

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadData = FileUploader::handleUpload($file, "tickets/{$ticket->code}");

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
