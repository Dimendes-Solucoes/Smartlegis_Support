<?php

namespace App\Services;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAttachment;
use App\Models\Helpdesk\TicketMessage;
use App\Libraries\FileUploader;
use App\Models\Credential;
use App\Models\Helpdesk\TicketStatus;
use App\Models\Helpdesk\TicketType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class TicketService
{
    public function list(array $filter): LengthAwarePaginator
    {
        $startDate = $filter['start_date'] ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $filter['end_date'] ?? Carbon::now()->endOfMonth()->toDateString();

        $query = Ticket::query()
            ->with(['type', 'status', 'author', 'tenants'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $query->when($filter['ticket_type_id'] ?? null, function ($query, $typeId) {
            $query->where('ticket_type_id', $typeId);
        });

        $query->when($filter['ticket_status_id'] ?? null, function ($query, $statusId) {
            $query->where('ticket_status_id', $statusId);
        });

        $query->when($filter['author_id'] ?? null, function ($query, $authorId) {
            $query->where('author_id', $authorId);
        });

        $query->when($filter['search'] ?? null, function ($query, $search) {
            $query->where('title', 'ilike', "%{$search}%");
        });

        return $query->orderBy('id', 'desc')->paginate(15);
    }

    public function prepareForCreate()
    {
        $types = TicketType::orderBy('title')->get();
        $status = TicketStatus::orderBy('id')->get();
        $credentials = Credential::orderBy('city_name')->get();

        return [
            'ticket_types' => $types,
            'ticket_status' => $status,
            'tenants' => $credentials
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

            if (!empty($data['tenants'])) {
                $ticket->tenants()->attach($data['tenants']);
            }

            if (!empty($data['attachments'])) {
                $this->handleAttachmentUpload($ticket, $data['attachments']);
            }

            return $ticket;
        });
    }

    public function update(int $id, array $data): Ticket
    {
        $ticket = Ticket::findOrFail($id);

        return DB::transaction(function () use ($ticket, $data) {
            $ticketData = Arr::only($data, [
                'ticket_type_id',
                'ticket_status_id',
                'title',
                'description'
            ]);

            $ticket->update($ticketData);

            if (isset($data['tenants'])) {
                $ticket->tenants()->sync($data['tenants']);
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
            'tenant_id' => $user->tenant_id,
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

    private function handleAttachmentUpload(Ticket $ticket, array $files): void
    {
        $user = Auth::user();
        $tenantId = $ticket->tenant_id;

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {

                $path = "tenants/{$tenantId}/tickets/{$ticket->id}";
                $uploadData = FileUploader::handleUpload($file, $path);

                if (!empty($uploadData)) {
                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'tenant_id' => $tenantId,
                        'user_id' => $user->id,
                        'file_name' => $uploadData['file_name'],
                        'file_path' => $uploadData['file_path'],
                    ]);
                }
            }
        }
    }
}
