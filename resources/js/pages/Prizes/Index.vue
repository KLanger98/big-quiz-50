<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent } from '@/components/ui/card';
import { prizes } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import { Gift } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Prizes',
        href: prizes.url(),
    },
];
</script>

<template>
    <Head title="Prizes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col items-center justify-center gap-6 p-4 font-[Helvetica,Verdana,Arial,sans-serif]">
            <Card class="max-w-md border-none bg-[#677D74] shadow-lg">
                <CardContent class="flex flex-col items-center gap-6 py-12">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-[#FBE3E0]/10">
                        <Gift class="h-12 w-12 text-[#FBE3E0]" />
                    </div>
                    <div class="text-center">
                        <h1 class="text-2xl font-bold text-[#FBE3E0]">Prizes Coming Soon!</h1>
                        <p class="mt-2 text-[#FBE3E0]/70">
                            We are working on exciting prizes for our top performers.
                            Check back soon for updates!
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
