<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Factory,
    Folder,
    LayoutGrid,
    List,
    MapPin,
    Tag,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
import { dashboard } from '@/routes';
import locations from '@/routes/locations';
import categories from '@/routes/categories';
import assets from '@/routes/assets';
import manufacturers from '@/routes/manufacturers';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        roles: ['super_admin', 'inventory_manager', 'inventory_user'],
    },
    {
        title: 'Assets',
        href: assets.index().url,
        icon: List,
        roles: ['super_admin', 'inventory_manager'],
    },
    {
        title: 'Categories',
        href: categories.index().url,
        icon: Folder,
        roles: ['super_admin'],
    },
    {
        title: 'Locations',
        href: locations.index().url,
        icon: MapPin,
        roles: ['super_admin', 'inventory_manager'],
    },
    {
        title: 'Manufacturers',
        href: manufacturers.index().url,
        icon: Factory,
        roles: ['super_admin', 'inventory_user'],
    },
];

const page = usePage();
const userRole = computed(() => page.props.auth?.user?.role || null);

const filteredNavItems = computed(() => {
    return mainNavItems.filter((item) => {
        if (!item.roles) return true;
        if (!userRole.value) return false;
        return item.roles.includes(userRole.value);
    });
});

const footerNavItems: NavItem[] = [
    {
        title: 'About Us',
        href: '/about',
        icon: Tag,
        roles: ['super_admin', 'inventory_manager', 'inventory_user'],
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
        roles: ['super_admin', 'inventory_manager', 'inventory_user'],
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
