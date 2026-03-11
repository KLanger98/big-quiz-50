<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as weeksIndex, show as weekShow } from '@/routes/weeks';
import type { BreadcrumbItem } from '@/types';
import { Calendar, Users, ChevronRight } from 'lucide-vue-next';

type Week = {
    id: number;
    week_number: number;
    start_date: string;
    end_date: string;
    scores_count: number;
};

type Competition = {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    max_score: number;
} | null;

type Props = {
    competition: Competition;
    weeks: Week[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Weeks',
        href: weeksIndex.url(),
    },
];

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
}

function formatDateFull(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Weeks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 font-[Helvetica,Verdana,Arial,sans-serif]">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-[#FBE3E0]">
                    <span v-if="competition">{{ competition.name }} - </span>Weeks
                </h1>
                <p class="mt-1 text-sm text-[#FBE3E0]/70">Browse all quiz weeks</p>
            </div>

            <!-- Weeks Grid -->
            <div v-if="weeks.length === 0" class="py-12 text-center">
                <Calendar class="mx-auto h-12 w-12 text-[#FBE3E0]/40" />
                <p class="mt-4 text-lg text-[#FBE3E0]/60">No weeks have been created yet.</p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="week in weeks"
                    :key="week.id"
                    :href="weekShow.url(week.id)"
                    class="group block"
                >
                    <Card class="h-full border-none bg-[#677D74] shadow-lg transition-all group-hover:bg-[#FBE3E0] group-hover:shadow-xl">
                        <CardHeader class="pb-3">
                            <CardTitle class="flex items-center justify-between text-[#FBE3E0] group-hover:text-[#677D74]">
                                <span class="text-lg">Week {{ week.week_number }}</span>
                                <ChevronRight class="h-5 w-5 text-[#FBE3E0]/50 transition-transform group-hover:translate-x-1 group-hover:text-[#677D74]" />
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 text-sm text-[#FBE3E0]/80 group-hover:text-[#677D74]/80">
                                    <Calendar class="h-4 w-4" />
                                    <span>{{ formatDate(week.start_date) }} - {{ formatDate(week.end_date) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#FBE3E0]/80 group-hover:text-[#677D74]/80">
                                    <Users class="h-4 w-4" />
                                    <span>{{ week.scores_count }} {{ week.scores_count === 1 ? 'score' : 'scores' }} submitted</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
