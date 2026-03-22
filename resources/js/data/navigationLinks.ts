import {
    UserIcon,
    HomeIcon,
    ArrowLeftEndOnRectangleIcon,
    UsersIcon,
    NewspaperIcon,
    CalendarDateRangeIcon,
    Cog6ToothIcon,
    ClockIcon,
    ClipboardDocumentListIcon,
    ShieldCheckIcon,
    UserGroupIcon,
    PencilSquareIcon,
    DocumentTextIcon,
    KeyIcon,
    TicketIcon,
    DocumentDuplicateIcon,
    FlagIcon,
    IdentificationIcon,
    BookOpenIcon,
    HashtagIcon,
} from '@heroicons/vue/24/outline';
import type { Method } from "@inertiajs/core";

interface NavigationLink {
    label?: string;
    icon?: any;
    route?: string;
    condition?: boolean | ((isRoot: boolean) => boolean);
    method?: Method;
    as?: string;
    textClass?: string;
    iconClass?: string;
    type?: 'link' | 'separator';
    children?: NavigationLink[];
}

export const staticNavigationLinks: NavigationLink[] = [
    {
        label: 'Dashboard',
        icon: HomeIcon,
        route: 'dashboard',
        type: 'link',
    },
    {
        label: 'Calendário',
        icon: CalendarDateRangeIcon,
        route: 'calendar.index',
        type: 'link',
    },
    {
        label: 'Configurações',
        icon: Cog6ToothIcon,
        route: 'tenant.settings',
        type: 'link',
    },
    {
        label: 'Credenciais',
        icon: KeyIcon,
        route: 'credentials.index',
        type: 'link',
    },
    {
        label: 'Tickets',
        icon: TicketIcon,
        route: 'tickets.index',
        type: 'link',
    },
    {
        label: 'Administradores',
        icon: ShieldCheckIcon,
        route: 'admin.index',
        type: 'link',
    },
    {
        label: 'Clicksign',
        icon: PencilSquareIcon,
        route: 'clicksign.index',
        type: 'link',
    },

    { type: 'separator' },
    {
        label: 'Legislaturas',
        icon: BookOpenIcon,
        route: 'legislatures.index',
        type: 'link',
    },
    {
        label: 'Partidos',
        icon: FlagIcon,
        route: 'category-parties.index',
        type: 'link',
    },
    {
        label: 'Usuários',
        icon: UsersIcon,
        route: 'users.index',
        type: 'link',
    },
    {
        label: 'Vereadores',
        icon: IdentificationIcon,
        route: 'councilors.index',
        type: 'link',
    },
    {
        label: 'Comissões',
        icon: UserGroupIcon,
        route: 'commissions.index',
        type: 'link',
    },
    {
        label: 'Tempos',
        icon: ClockIcon,
        route: 'timers.index',
        type: 'link',
    },
    {
        label: 'Categorias',
        icon: NewspaperIcon,
        route: 'document-categories.index',
        type: 'link',
    },
    {
        label: 'Protocolos',
        icon: HashtagIcon,
        route: 'protocol-minimums.index',
        type: 'link',
    },
    {
        label: 'Modelos',
        icon: DocumentDuplicateIcon,
        route: 'document-models.index',
        type: 'link',
    },
    {
        label: 'Documentos',
        icon: DocumentTextIcon,
        route: 'documents.index',
        type: 'link',
    },
    {
        label: 'Sessões',
        icon: ClipboardDocumentListIcon,
        route: 'sessions.index',
        type: 'link',
    },

    { type: 'separator' },
    {
        label: 'Perfil',
        icon: UserIcon,
        route: 'profile.edit',
        type: 'link',
    },
    {
        label: 'Sair',
        icon: ArrowLeftEndOnRectangleIcon,
        route: 'logout',
        method: 'post',
        as: 'button',
        textClass: 'text-red-500',
        iconClass: 'text-red-500',
        type: 'link',
    },
];