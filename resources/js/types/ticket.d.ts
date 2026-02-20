export interface TicketStatus {
    value: 'new' | 'open' | 'pending' | 'resolved' | 'closed' | 'canceled'; 
    title: string;
    color: string;
}

export interface TicketType {
    id: number;
    title: string;
}

export interface Author {
    id: number;
    name: string;
}

interface Credential {
    id: string;
    city_name: string;
}

interface TicketAttachement {
    id: number;
    user: User;
    file_name: string;
    file_path: string;
    file_url: string;
    created_at: string;
}

interface TicketMessage {
    id: number;
    content: string;
    author: User;
    created_at: string;
}

export interface Ticket {
    id: number;
    code: string;
    title: string;
    description: string;
    ticket_type_id: number;
    author_id: number;
    status: TicketStatus;
    status_details: TicketStatus;
    type: TicketType;
    author: Author;
    created_at: string;
    updated_at: string;
    messages_count?: number;
    attachments_count?: number;
    credentials: Credential[];
    messages: TicketMessage[];
    attachments: TicketAttachement[];
}

export interface PaginatedTickets {
    data: Ticket[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

export interface TicketFilters {
    search?: string;
    ticket_type_id?: number;
    status?: string;
    author_id?: number;
}