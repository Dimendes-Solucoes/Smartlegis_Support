export interface TicketStatus {
    id: number;
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

export interface Ticket {
    id: number;
    code: string;
    title: string;
    description: string;
    ticket_status_id: number;
    ticket_type_id: number;
    author_id: number;
    status: TicketStatus;
    type: TicketType;
    author: Author;
    created_at: string;
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
    ticket_status_id?: number;
}