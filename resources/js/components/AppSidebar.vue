<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Trophy, Calendar, Gift, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
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
import { leaderboard, prizes } from '@/routes';
import { index as weeksIndex } from '@/routes/weeks';
import { edit as competitionEdit } from '@/routes/competition';
import { edit as profileEdit } from '@/routes/profile';
import type { NavItem } from '@/types';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.is_admin);

const mainNavItems: NavItem[] = [
    {
        title: 'Leaderboard',
        href: leaderboard(),
        icon: Trophy,
    },
    {
        title: 'Weekly Scores',
        href: weeksIndex(),
        icon: Calendar,
    },
    {
        title: 'Prizes',
        href: prizes(),
        icon: Gift,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'Competition Settings',
        href: competitionEdit(),
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Settings',
        href: profileEdit(),
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="leaderboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain v-if="isAdmin" :items="adminNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
