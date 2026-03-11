<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import confetti from 'canvas-confetti';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { leaderboard } from '@/routes';
import { week as weekRoute } from '@/routes/leaderboard';
import type { BreadcrumbItem } from '@/types';
import { Trophy, Crown, Medal, ChevronLeft, ChevronRight, Sparkles, UtensilsCrossed } from 'lucide-vue-next';
import { computed } from 'vue';

type Standing = {
    rank: number;
    name: string;
    team_name: string | null;
    profile_picture: string | null;
    score: number;
    average_score: number;
    weeks_played: number;
    is_winner: boolean;
};

type Week = {
    id: number;
    week_number: number;
    start_date: string;
    end_date: string;
    prev_week_id: number | null;
    next_week_id: number | null;
};

type Competition = {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    max_score: number;
} | null;

type Props = {
    week: Week;
    standings: Standing[];
    competition: Competition;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Leaderboard',
        href: leaderboard.url(),
    },
    {
        title: `Week ${props.week.week_number}`,
        href: weekRoute.url(props.week.id),
    },
];

const winner = props.standings.find((s) => s.is_winner);

const lastRank = computed(() => {
    const ranks = props.standings.map((s) => s.rank);
    return ranks.length > 1 ? Math.max(...ranks) : 0;
});

onMounted(() => {
    if (winner) {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.3 },
            colors: ['#FBE3E0', '#FFD700', '#677D74'],
        });
    }
});

function getRankIcon(rank: number) {
    if (rank === 1) return Crown;
    if (rank === 2) return Medal;
    if (rank === 3) return Trophy;
    if (lastRank.value > 1 && rank === lastRank.value) return UtensilsCrossed;
    return null;
}

function getRankColor(rank: number): string {
    if (rank === 1) return 'text-yellow-400';
    if (rank === 2) return 'text-gray-300';
    if (rank === 3) return 'text-amber-600';
    if (lastRank.value > 1 && rank === lastRank.value) return 'text-orange-400';
    return '';
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}
</script>

<template>
    <Head :title="`Week ${week.week_number} Leaderboard`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 font-[Helvetica,Verdana,Arial,sans-serif]">
            <!-- Week Navigation -->
            <div class="flex items-center justify-between">
                <Link
                    v-if="week.prev_week_id"
                    :href="weekRoute.url(week.prev_week_id)"
                    class="inline-flex items-center gap-1 rounded-full bg-[#FBE3E0] px-4 py-2 text-sm font-medium text-[#677D74] transition-colors hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                >
                    <ChevronLeft class="h-4 w-4" />
                    Previous Week
                </Link>
                <div v-else />

                <div class="text-center">
                    <h1 class="text-2xl font-bold text-[#FBE3E0]">Week {{ week.week_number }}</h1>
                    <p class="text-sm text-[#FBE3E0]/70">
                        {{ formatDate(week.start_date) }} - {{ formatDate(week.end_date) }}
                    </p>
                </div>

                <Link
                    v-if="week.next_week_id"
                    :href="weekRoute.url(week.next_week_id)"
                    class="inline-flex items-center gap-1 rounded-full bg-[#FBE3E0] px-4 py-2 text-sm font-medium text-[#677D74] transition-colors hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                >
                    Next Week
                    <ChevronRight class="h-4 w-4" />
                </Link>
                <div v-else />
            </div>

            <!-- Winner Effect -->
            <Card v-if="winner" class="border-2 border-yellow-400/50 bg-gradient-to-r from-[#677D74] via-[#5a7068] to-[#677D74] shadow-lg shadow-yellow-400/10">
                <CardContent class="flex flex-col items-center gap-3 py-6">
                    <div class="relative">
                        <Sparkles class="absolute -left-3 -top-3 h-6 w-6 animate-pulse text-yellow-400" />
                        <Sparkles class="absolute -right-3 -top-3 h-6 w-6 animate-pulse text-yellow-400" />
                        <img
                            v-if="winner.profile_picture"
                            :src="winner.profile_picture"
                            :alt="winner.name"
                            class="h-16 w-16 rounded-full border-2 border-yellow-400 object-cover"
                        />
                        <div
                            v-else
                            class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-yellow-400 bg-[#FBE3E0]/20 text-2xl font-bold text-[#FBE3E0]"
                        >
                            {{ winner.name.charAt(0).toUpperCase() }}
                        </div>
                    </div>
                    <div class="text-center">
                        <Crown class="mx-auto mb-1 h-6 w-6 text-yellow-400" />
                        <h2 class="text-xl font-bold text-[#FBE3E0]">{{ winner.name }}</h2>
                        <p v-if="winner.team_name" class="text-sm text-[#FBE3E0]/70">{{ winner.team_name }}</p>
                        <p class="mt-1 text-2xl font-black text-yellow-400">{{ winner.score }} pts</p>
                        <p class="text-xs text-[#FBE3E0]/60">Week {{ week.week_number }} Leader</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Standings Table -->
            <Card class="border-none bg-[#677D74] shadow-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                        <Trophy class="h-5 w-5" />
                        Standings
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="standings.length === 0" class="py-8 text-center text-[#FBE3E0]/60">
                        No scores submitted for this week yet.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-[#FBE3E0]/20">
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70">Rank</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70">Player</th>
                                    <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="standing in standings"
                                    :key="standing.rank"
                                    class="border-b border-[#FBE3E0]/10 transition-colors hover:bg-[#FBE3E0] hover:text-[#677D74]"
                                    :class="{ 'bg-[#FBE3E0]/10': standing.is_winner }"
                                >
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-1">
                                            <component
                                                :is="getRankIcon(standing.rank)"
                                                v-if="getRankIcon(standing.rank)"
                                                class="h-4 w-4"
                                                :class="getRankColor(standing.rank)"
                                            />
                                            <span class="font-bold text-[#FBE3E0]">{{ standing.rank }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="standing.profile_picture"
                                                :src="standing.profile_picture"
                                                :alt="standing.name"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#FBE3E0]/20 text-xs font-bold text-[#FBE3E0]"
                                            >
                                                {{ standing.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-[#FBE3E0]">{{ standing.name }}</div>
                                                <div v-if="standing.team_name" class="text-xs text-[#FBE3E0]/60">{{ standing.team_name }}</div>
                                            </div>
                                            <span v-if="standing.is_winner" class="ml-1 inline-flex items-center rounded-full bg-yellow-400/20 px-2 py-0.5 text-xs font-medium text-yellow-300">
                                                Leader
                                            </span>
                                            <span v-if="lastRank > 1 && standing.rank === lastRank" class="ml-1 inline-flex items-center rounded-full bg-orange-400/20 px-2 py-0.5 text-xs font-medium text-orange-300">
                                                Wooden Spoon
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-[#FBE3E0]">{{ standing.score }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Back to Overall -->
            <div class="text-center">
                <Link
                    :href="leaderboard.url()"
                    class="inline-flex items-center gap-2 rounded-full bg-[#FBE3E0] px-6 py-2.5 font-medium text-[#677D74] transition-colors hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                >
                    <ChevronLeft class="h-4 w-4" />
                    Back to Overall Leaderboard
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
