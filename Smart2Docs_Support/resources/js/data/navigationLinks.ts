import {
    UserIcon,
    HomeIcon,
    ArrowLeftEndOnRectangleIcon,
    UsersIcon,
    Cog6ToothIcon
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
        label: 'Clientes',
        icon: UsersIcon,
        route: 'clients.index',
        type: 'link',
    },
    {
        label: 'Configurações',
        icon: Cog6ToothIcon,
        route: 'tenant.settings',
        type: 'link',
    },

    { type: 'separator' },
    {
        label: 'Usuários',
        icon: UsersIcon,
        route: 'users.index',
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