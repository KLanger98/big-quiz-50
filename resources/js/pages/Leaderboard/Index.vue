<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { leaderboard } from '@/routes';
import { week as weekRoute } from '@/routes/leaderboard';
import type { BreadcrumbItem } from '@/types';
import { Trophy, Crown, Medal, UtensilsCrossed } from 'lucide-vue-next';
import { computed } from 'vue';

type Standing = {
    rank: number;
    name: string;
    team_name: string | null;
    profile_picture: string | null;
    total_score: number;
    average_score: number;
    weeks_played: number;
    is_winner: boolean;
};

type WeekStanding = {
    rank: number;
    name: string;
    team_name: string | null;
    profile_picture: string | null;
    score: number;
    average_score: number;
    weeks_played: number;
    is_winner: boolean;
};

type Competition = {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    max_score: number;
} | null;

type CurrentWeek = {
    id: number;
    week_number: number;
    start_date: string;
    end_date: string;
} | null;

type Props = {
    competition: Competition;
    overallStandings: Standing[];
    currentWeek: CurrentWeek;
    currentWeekStandings: WeekStanding[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Leaderboard',
        href: leaderboard.url(),
    },
];

const lastOverallRank = computed(() => {
    const ranks = props.overallStandings.map((s) => s.rank);
    return ranks.length > 1 ? Math.max(...ranks) : 0;
});

const lastWeekRank = computed(() => {
    const ranks = props.currentWeekStandings.map((s) => s.rank);
    return ranks.length > 1 ? Math.max(...ranks) : 0;
});

function getRankIcon(rank: number, lastRank: number) {
    if (rank === 1) return Crown;
    if (rank === 2) return Medal;
    if (rank === 3) return Trophy;
    if (lastRank > 1 && rank === lastRank) return UtensilsCrossed;
    return null;
}

function getRankColor(rank: number, lastRank: number): string {
    if (rank === 1) return 'text-yellow-400';
    if (rank === 2) return 'text-gray-300';
    if (rank === 3) return 'text-amber-600';
    if (lastRank > 1 && rank === lastRank) return 'text-orange-400';
    return '';
}
</script>

<template>
    <Head title="Leaderboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 font-[Helvetica,Verdana,Arial,sans-serif]">
            <!-- Competition Header -->
            <div v-if="competition" class="text-center">
                <h1 class="text-3xl font-bold text-[#FBE3E0]">{{ competition.name }}</h1>
                <p class="mt-1 text-sm text-[#FBE3E0]/70">Leaderboard</p>
            </div>

            <!-- Overall Standings -->
            <Card class="border-none bg-[#677D74] shadow-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                        <Trophy class="h-5 w-5" />
                        Overall Standings
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="overallStandings.length === 0" class="py-8 text-center text-[#FBE3E0]/60">
                        No standings yet. Scores will appear once weeks are played.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-[#FBE3E0]/20">
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70">Rank</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70">Player</th>
                                    <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70">Total</th>
                                    <th class="hidden px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70 sm:table-cell">Average</th>
                                    <th class="hidden px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-[#FBE3E0]/70 sm:table-cell">Weeks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="standing in overallStandings"
                                    :key="standing.rank"
                                    class="group border-b border-[#FBE3E0]/10 transition-colors hover:bg-[#FBE3E0]"
                                    :class="{ 'bg-[#FBE3E0]/10': standing.is_winner }"
                                >
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-1">
                                            <component
                                                :is="getRankIcon(standing.rank, lastOverallRank)"
                                                v-if="getRankIcon(standing.rank, lastOverallRank)"
                                                class="h-4 w-4"
                                                :class="getRankColor(standing.rank, lastOverallRank)"
                                            />
                                            <span class="font-bold text-[#FBE3E0] group-hover:text-[#677D74]">{{ standing.rank }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="standing.profile_picture"
                                                :src="standing.profile_picture"
                                                :alt="standing.name"
                                                class="h-10 w-10 rounded-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FBE3E0]/20 text-sm font-bold text-[#FBE3E0] group-hover:bg-[#677D74]/20 group-hover:text-[#677D74]"
                                            >
                                                {{ standing.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-[#FBE3E0] group-hover:text-[#677D74]">{{ standing.name }}</div>
                                                <div v-if="standing.team_name" class="text-xs text-[#FBE3E0]/60 group-hover:text-[#677D74]/60">{{ standing.team_name }}</div>
                                            </div>
                                            <span v-if="standing.rank === 1" class="ml-1 inline-flex items-center rounded-full bg-yellow-400/20 px-2 py-0.5 text-xs font-medium text-yellow-300">
                                                Leader
                                            </span>
                                            <span v-if="lastOverallRank > 1 && standing.rank === lastOverallRank" class="ml-1 inline-flex items-center rounded-full bg-orange-400/20 px-2 py-0.5 text-xs font-medium text-orange-300">
                                                Wooden Spoon
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-[#FBE3E0] group-hover:text-[#677D74]">{{ standing.total_score }}</td>
                                    <td class="hidden px-3 py-3 text-right text-[#FBE3E0]/80 group-hover:text-[#677D74]/80 sm:table-cell">{{ standing.average_score.toFixed(1) }}</td>
                                    <td class="hidden px-3 py-3 text-right text-[#FBE3E0]/80 group-hover:text-[#677D74]/80 sm:table-cell">{{ standing.weeks_played }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <Separator class="bg-[#FBE3E0]/20" />

            <!-- Current Week Standings -->
            <Card class="border-none bg-[#677D74] shadow-lg">
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-[#FBE3E0]">
                        <span class="flex items-center gap-2">
                            <Medal class="h-5 w-5" />
                            <span v-if="currentWeek">Week {{ currentWeek.week_number }} Standings</span>
                            <span v-else>Current Week</span>
                        </span>
                        <Link
                            v-if="currentWeek"
                            :href="weekRoute.url(currentWeek.id)"
                            class="rounded-full bg-[#FBE3E0] px-4 py-1.5 text-sm font-medium text-[#677D74] transition-colors hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                        >
                            View Full Week
                        </Link>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="!currentWeek" class="py-8 text-center text-[#FBE3E0]/60">
                        No active week at the moment.
                    </div>
                    <div v-else-if="currentWeekStandings.length === 0" class="py-8 text-center text-[#FBE3E0]/60">
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
                                    v-for="standing in currentWeekStandings"
                                    :key="standing.rank"
                                    class="group border-b border-[#FBE3E0]/10 transition-colors hover:bg-[#FBE3E0]"
                                    :class="{ 'bg-[#FBE3E0]/10': standing.is_winner }"
                                >
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-1">
                                            <component
                                                :is="getRankIcon(standing.rank, lastWeekRank)"
                                                v-if="getRankIcon(standing.rank, lastWeekRank)"
                                                class="h-4 w-4"
                                                :class="getRankColor(standing.rank, lastWeekRank)"
                                            />
                                            <span class="font-bold text-[#FBE3E0] group-hover:text-[#677D74]">{{ standing.rank }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="standing.profile_picture"
                                                :src="standing.profile_picture"
                                                :alt="standing.name"
                                                class="h-10 w-10 rounded-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FBE3E0]/20 text-sm font-bold text-[#FBE3E0] group-hover:bg-[#677D74]/20 group-hover:text-[#677D74]"
                                            >
                                                {{ standing.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-[#FBE3E0] group-hover:text-[#677D74]">{{ standing.name }}</div>
                                                <div v-if="standing.team_name" class="text-xs text-[#FBE3E0]/60 group-hover:text-[#677D74]/60">{{ standing.team_name }}</div>
                                            </div>
                                            <span v-if="standing.is_winner" class="ml-1 inline-flex items-center rounded-full bg-yellow-400/20 px-2 py-0.5 text-xs font-medium text-yellow-300">
                                                Leader
                                            </span>
                                            <span v-if="lastWeekRank > 1 && standing.rank === lastWeekRank" class="ml-1 inline-flex items-center rounded-full bg-orange-400/20 px-2 py-0.5 text-xs font-medium text-orange-300">
                                                Wooden Spoon
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-[#FBE3E0] group-hover:text-[#677D74]">{{ standing.score }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
