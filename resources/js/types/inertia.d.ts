import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { Errors, Page, PageProps } from '@inertiajs/vue3';

interface FlashMessages {
    success?: string;
    error?: string;
    message?: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Tenant {
    id: string;
    name: string;
    city: string;
}

// TYPES OF SYSTEM
interface Tribune {
    id: number;
}

interface Discussion {
    id: number;
}

interface BigDiscussion {
    id: number;
}

interface QuestionOrder {
    id: number;
}

interface Quorum {
    id: number;
    tribunes: Tribune[];
    discussions: Discussion[];
    big_discussions: BigDiscussion[];
    question_orders: QuestionOrder[];
}

interface CustomPageProps extends PageProps {
    auth: {
        user: User;
    };
    flash: FlashMessages;
    tenants: Tenant[];
}

declare module '@inertiajs/vue3' {
    export interface PageProps extends CustomPageProps { }
}

declare module '@inertiajs/core' {
    export interface PageProps extends CustomPageProps { }
}

declare global {
    var route: typeof ZiggyRoute;
}