<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { edit as competitionEdit, update as competitionUpdate } from '@/routes/competition';
import type { BreadcrumbItem } from '@/types';
import { Settings } from 'lucide-vue-next';

type Competition = {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    max_score: number;
} | null;

type Props = {
    competition: Competition;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Competition Settings',
        href: competitionEdit.url(),
    },
];

const form = useForm({
    name: props.competition?.name ?? '',
    start_date: props.competition?.start_date ?? '',
    end_date: props.competition?.end_date ?? '',
    max_score: props.competition?.max_score ?? 50,
});

function submit() {
    form.put(competitionUpdate.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Competition Settings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 font-[Helvetica,Verdana,Arial,sans-serif]">
            <div class="mx-auto w-full max-w-2xl">
                <Card class="border-none bg-[#677D74] shadow-lg">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                            <Settings class="h-5 w-5" />
                            Competition Settings
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid gap-2">
                                <Label for="name" class="text-[#FBE3E0]">Competition Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0] placeholder:text-[#FBE3E0]/40"
                                    placeholder="e.g., The Big Quiz 50"
                                    required
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="start_date" class="text-[#FBE3E0]">Start Date</Label>
                                    <Input
                                        id="start_date"
                                        v-model="form.start_date"
                                        type="date"
                                        class="border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0]"
                                        required
                                    />
                                    <InputError :message="form.errors.start_date" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="end_date" class="text-[#FBE3E0]">End Date</Label>
                                    <Input
                                        id="end_date"
                                        v-model="form.end_date"
                                        type="date"
                                        class="border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0]"
                                        required
                                    />
                                    <InputError :message="form.errors.end_date" />
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="max_score" class="text-[#FBE3E0]">Maximum Score Per Week</Label>
                                <Input
                                    id="max_score"
                                    v-model="form.max_score"
                                    type="number"
                                    min="1"
                                    class="border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0] placeholder:text-[#FBE3E0]/40"
                                    placeholder="50"
                                    required
                                />
                                <InputError :message="form.errors.max_score" />
                            </div>

                            <div class="flex items-center gap-4">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-full bg-[#FBE3E0] text-[#677D74] hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                                >
                                    Save Settings
                                </Button>

                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p
                                        v-show="form.recentlySuccessful"
                                        class="text-sm text-[#FBE3E0]/70"
                                    >
                                        Saved.
                                    </p>
                                </Transition>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
