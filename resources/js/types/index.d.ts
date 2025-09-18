import { Config } from 'ziggy-js';

//Users
export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    path_image: string | null;
    nickname: string | null;
    category: { id: number; name: string } | null;
}
export interface Category {
    id: number;
    name: string;
}
export interface Mayor {
    id: number;
    name: string;
    email: string;
}

//Tribunes
export interface Tribune {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    }
}
export interface TribuneData {
    tribune: Tribune;
    tribune_users: TribuneUser[];
    apartiamento_users: ApartiamentoUser[];
}
export interface TribuneUser {
    id: number;
    user: User;
}
export interface ApartiamentoUser {
    id: number;
    user: User;
}

//Admin
export interface Admin {
    id: number;
    name: string;
    email: string;
}


//Calendar 
export interface CalendarEvent {
    id: number;
    title: string;
    start: string;
    end: string | null;
    status_id: number;
    status_name: string;
    tenant_city: string;
}
export interface CalendarData {
    year: number;
    month: number;
    monthName: string;
    events: CalendarEvent[];
}
export interface EventStatusDetail {
    bg: string;
    text: string;
    label: string;
}
export interface EventStatusColorsMap {
    [key: number]: EventStatusDetail;
}

//Clicksign
export interface Clicksign {
    tenant_id: number;
    tenant_city: string;
    quantity: string;
}

//Tenant
export interface Tenant {
    id: string;
    name: string;
    city: string;
}

//Timer
export interface Timer {
    id: number;
    title: string;
    locale: string;
    timer: string;
}

//Sessions 
export interface Session {
    id: number;
    name: string;
    datetime_start: string;
    session_status_id: number;
}
export interface SessionStatus {
    id: number;
    name: string;
}
export interface Document {
    id: number;
    name: string;
    attachment: string;
}
export interface Vote {
    id: number | null;
    vote_category_id: number | null;
    user: User;
}
export interface VoteCategory {
    id: number;
    name: string;
}
export interface PaginatedSessions {
    data: Session[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}
export interface Quorum {
    session_id: number
}
export interface Discussion {
    id: number;
    session_name: string;
    document_name: string;
    quorum: Quorum
}
export interface PaginatedDiscussions {
    data: Discussion[];
    links: { url: string | null; label: string; active: boolean; }[];
}
export interface DiscussionData {
    session: {
        id: number;
        name: string;
    };
    discussions: PaginatedDiscussions;
}

//Quoruns
export interface QuorumUser {
    id: number;
    user: User;
}
export interface Quorum {
    id: number;
    session: {
        id: number;
        name: string;
    };
}
export interface QuorumData {
    quorum: Quorum;
    quorum_users: QuorumUser[];
}

//Question Orders
export interface QuestionOrderUser {
    id: number;
    user: User;
}
export interface QuestionOrder {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    };
}
export interface QuestionOrderData {
    question_order: QuestionOrder;
    users: QuestionOrderUser[];
}


//Documents
export interface Document {
    id: number;
    name: string;
    protocol_number: string | null;
    attachment_url: string | null;
    document_status_vote_id: number;
    document_status_movement_id: number;
    status_sign: number;
}
export interface PaginatedDocuments {
    data: Document[];
    links: { url: string | null; label: string; active: boolean; }[];
}
export interface EditData {
    document: Document;
}
export interface DocumentCategory {
    id: number;
    name: string;
    abbreviation: string;
    order: number;
    min_protocol: number;
    highest_protocol: number | null;
    is_active: boolean;
}
export interface Category {
    id: number;
    name: string;
    abbreviation: string | null;
    min_protocol: number;
    order: number;
}

//Discussions
export interface Discussion {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    };
    document: {
        name: string;
    };
}
export interface DiscussionData {
    discussion: Discussion;
    users: DiscussionUser[];
}

//Councilors
export interface User {
    id: number;
    name: string;
    email: string;
    path_image: string | null;
    nickname: string | null;
    category: { id: number; name: string } | null;
    party: { id: number; name_party: string } | null;
    status_lider: string | null;
    status_user: number;
    is_first_secretary: boolean | null;
}
export interface Party {
    id: number;
    name_party: string;
}
export interface Category {
    id: number;
    name: string;
}

//Comissions
export interface Commission {
    id: number;
    comission_name: string;
    type_description: string;
}
export interface CommissionType {
    id: number;
    title: string;
}
export interface User {
    id: number;
    name: string;
    function: number;
}
export interface CommissionUser {
    id: number;
    name: string;
    function: number | null;
}
export interface UserFunction {
    id: number;
    title: string;
}
export interface FormErrors {
    comission_name?: string;
    type?: string;
    users?: string;
    'users.id'?: string;
    'users.function'?: string;
    [key: string]: string | undefined;
}

//Big Discussions
export interface BigDiscussionUser {
    id: number;
    user: User;
}
export interface Discussion {
    id: number;
    quorum: {
        session: {
            id: number;
            name: string;
        }
    }
}
export interface DiscussionData {
    discussion: Discussion;
    users: BigDiscussionUser[];
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
