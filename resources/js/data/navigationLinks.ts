import {
    UserIcon,
    HomeIcon,
    ArrowLeftEndOnRectangleIcon,
    UsersIcon,
    NewspaperIcon,
    CalendarDateRangeIcon,
    BuildingOfficeIcon,
    Cog6ToothIcon,
    ClockIcon,
    ClipboardDocumentListIcon,
    ShieldCheckIcon,
    UserGroupIcon,
    MegaphoneIcon,
    ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/solid';

interface NavigationLink {
    label?: string;
    icon?: any;
    route?: string;
    condition?: boolean | ((isRoot: boolean) => boolean);
    method?: string;
    as?: string;
    textClass?: string;
    iconClass?: string;
    type?: 'link' | 'separator';
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
        label: 'Administradores',
        icon: ShieldCheckIcon,
        route: 'admin.index',
        type: 'link',
    },

    { type: 'separator' },
    {
        label: 'Usuários',
        icon: UsersIcon,
        route: 'users.index',
        type: 'link',
    },
    {
        label: 'Vereadores',
        icon: BuildingOfficeIcon,
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
        label: 'Categorias de Documentos',
        icon: NewspaperIcon,
        route: 'document-categories.index',
        type: 'link',
    },
        {
        label: 'Sessões',
        icon: ClipboardDocumentListIcon,
        route: 'sessions.index',
        type: 'link',
    },
    {
        label: 'Tempos',
        icon: ClockIcon,
        route: 'timers.index',
        type: 'link',
    },

    { type: 'separator' },

        {
        label: 'Tribunas',
        icon: MegaphoneIcon,
        route: 'tribunes.index',
        type: 'link',
        },

            {
        label: 'Explanações Pessoais',
        icon: ChatBubbleLeftRightIcon,
        route: 'big-discussions.index',
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