<script setup lang="ts">
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogClose,
} from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import { index as weeksIndex, show as weekShow } from '@/routes/weeks';
import { store as storeScore, update as updateScore, destroy as destroyScore } from '@/routes/scores';
import { store as storeComment, destroy as destroyComment } from '@/routes/comments';
import { toggle as toggleReaction } from '@/routes/reactions';
import type { BreadcrumbItem } from '@/types';
import { Calendar, Trophy, MessageCircle, Smile, Trash2, Pencil, Send } from 'lucide-vue-next';

type Week = {
    id: number;
    week_number: number;
    start_date: string;
    end_date: string;
};

type Competition = {
    id: number;
    name: string;
    start_date: string;
    end_date: string;
    max_score: number;
} | null;

type Score = {
    id: number;
    score: number;
    user: {
        id: number;
        name: string;
        team_name: string | null;
        profile_picture: string | null;
    };
};

type UserScore = {
    id: number;
    score: number;
} | null;

type Comment = {
    id: number;
    body: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        profile_picture: string | null;
    };
};

type Reaction = {
    emoji: string;
    count: number;
    reacted: boolean;
};

type Props = {
    week: Week;
    competition: Competition;
    scores: Score[];
    userScore: UserScore;
    comments: Comment[];
    reactions: Reaction[];
};

const props = defineProps<Props>();

const page = usePage();
const currentUserId = computed(() => page.props.auth.user.id);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Weeks',
        href: weeksIndex.url(),
    },
    {
        title: `Week ${props.week.week_number}`,
        href: weekShow.url(props.week.id),
    },
];

// Score form
const scoreForm = useForm({
    score: '' as string | number,
});

const isEditingScore = ref(false);
const editScoreForm = useForm({
    score: props.userScore?.score ?? ('' as string | number),
});

const showDeleteDialog = ref(false);

function submitScore() {
    scoreForm.post(storeScore.url(props.week.id), {
        preserveScroll: true,
        onSuccess: () => {
            scoreForm.reset();
        },
    });
}

function submitEditScore() {
    if (!props.userScore) return;
    editScoreForm.put(updateScore.url(props.userScore.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditingScore.value = false;
        },
    });
}

function deleteScore() {
    if (!props.userScore) return;
    router.delete(destroyScore.url(props.userScore.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
}

// Comment form
const commentForm = useForm({
    body: '',
});

function submitComment() {
    commentForm.post(storeComment.url(props.week.id), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset();
        },
    });
}

function deleteCommentById(commentId: number) {
    router.delete(destroyComment.url(commentId), {
        preserveScroll: true,
    });
}

// Reactions
const availableEmojis = ['👍', '🎉', '🔥', '😂', '😢', '🤔'];

function toggleEmoji(emoji: string) {
    router.post(
        toggleReaction.url(props.week.id),
        { emoji },
        { preserveScroll: true },
    );
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

function formatCommentDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head :title="`Week ${week.week_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 font-[Helvetica,Verdana,Arial,sans-serif]">
            <!-- Week Header -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-[#FBE3E0]">Week {{ week.week_number }}</h1>
                <p class="mt-1 flex items-center justify-center gap-2 text-sm text-[#FBE3E0]/70">
                    <Calendar class="h-4 w-4" />
                    {{ formatDate(week.start_date) }} - {{ formatDate(week.end_date) }}
                </p>
                <p v-if="competition" class="mt-0.5 text-xs text-[#FBE3E0]/50">{{ competition.name }}</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column: Scores -->
                <div class="flex flex-col gap-6 lg:col-span-2">
                    <!-- Score Entry / Edit -->
                    <Card class="border-none bg-[#677D74] shadow-lg">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                                <Trophy class="h-5 w-5" />
                                Your Score
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <!-- No score yet - show entry form -->
                            <form v-if="!userScore" @submit.prevent="submitScore" class="flex items-end gap-3">
                                <div class="flex-1">
                                    <Label for="score" class="text-[#FBE3E0]">Enter your score</Label>
                                    <Input
                                        id="score"
                                        v-model="scoreForm.score"
                                        type="number"
                                        min="0"
                                        :max="competition?.max_score"
                                        :placeholder="competition ? `0 - ${competition.max_score}` : 'Your score'"
                                        class="mt-1 border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0] placeholder:text-[#FBE3E0]/40"
                                        required
                                    />
                                    <InputError class="mt-1" :message="scoreForm.errors.score" />
                                </div>
                                <Button
                                    type="submit"
                                    :disabled="scoreForm.processing"
                                    class="rounded-full bg-[#FBE3E0] text-[#677D74] hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                                >
                                    Submit Score
                                </Button>
                            </form>

                            <!-- Has score - show it with edit/delete -->
                            <div v-else>
                                <div v-if="!isEditingScore" class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-[#FBE3E0]/70">Your submitted score</p>
                                        <p class="text-3xl font-black text-[#FBE3E0]">{{ userScore.score }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="isEditingScore = true; editScoreForm.score = userScore!.score"
                                            class="rounded-full border-[#FBE3E0]/30 text-[#FBE3E0] hover:bg-[#FBE3E0] hover:text-[#677D74]"
                                        >
                                            <Pencil class="mr-1 h-3 w-3" />
                                            Edit
                                        </Button>
                                        <Dialog v-model:open="showDeleteDialog">
                                            <DialogTrigger as-child>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    class="rounded-full border-red-400/30 text-red-400 hover:bg-red-400 hover:text-white"
                                                >
                                                    <Trash2 class="mr-1 h-3 w-3" />
                                                    Delete
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent class="bg-[#677D74] text-[#FBE3E0]">
                                                <DialogHeader>
                                                    <DialogTitle class="text-[#FBE3E0]">Delete Score</DialogTitle>
                                                    <DialogDescription class="text-[#FBE3E0]/70">
                                                        Are you sure you want to delete your score of {{ userScore.score }}? This action cannot be undone.
                                                    </DialogDescription>
                                                </DialogHeader>
                                                <DialogFooter class="gap-2">
                                                    <DialogClose as-child>
                                                        <Button
                                                            variant="outline"
                                                            class="rounded-full border-[#FBE3E0]/30 text-[#FBE3E0] hover:bg-[#FBE3E0] hover:text-[#677D74]"
                                                        >
                                                            Cancel
                                                        </Button>
                                                    </DialogClose>
                                                    <Button
                                                        @click="deleteScore"
                                                        class="rounded-full bg-red-500 text-white hover:bg-red-600"
                                                    >
                                                        Delete Score
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </div>

                                <!-- Editing score -->
                                <form v-else @submit.prevent="submitEditScore" class="flex items-end gap-3">
                                    <div class="flex-1">
                                        <Label for="edit-score" class="text-[#FBE3E0]">Update your score</Label>
                                        <Input
                                            id="edit-score"
                                            v-model="editScoreForm.score"
                                            type="number"
                                            min="0"
                                            :max="competition?.max_score"
                                            class="mt-1 border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0] placeholder:text-[#FBE3E0]/40"
                                            required
                                        />
                                        <InputError class="mt-1" :message="editScoreForm.errors.score" />
                                    </div>
                                    <Button
                                        type="submit"
                                        :disabled="editScoreForm.processing"
                                        class="rounded-full bg-[#FBE3E0] text-[#677D74] hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                                    >
                                        Save
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="isEditingScore = false"
                                        class="rounded-full border-[#FBE3E0]/30 text-[#FBE3E0] hover:bg-[#FBE3E0] hover:text-[#677D74]"
                                    >
                                        Cancel
                                    </Button>
                                </form>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- All Scores -->
                    <Card class="border-none bg-[#677D74] shadow-lg">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                                <Trophy class="h-5 w-5" />
                                All Scores
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="scores.length === 0" class="py-6 text-center text-[#FBE3E0]/60">
                                No scores submitted yet. Be the first!
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="(scoreEntry, index) in scores"
                                    :key="scoreEntry.id"
                                    class="group flex items-center justify-between rounded-lg border border-[#FBE3E0]/10 px-4 py-3 transition-colors hover:bg-[#FBE3E0]/10"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="min-w-[2rem] text-center text-sm font-bold text-[#FBE3E0]/70">
                                            #{{ index + 1 }}
                                        </span>
                                        <img
                                            v-if="scoreEntry.user.profile_picture"
                                            :src="scoreEntry.user.profile_picture"
                                            :alt="scoreEntry.user.name"
                                            class="h-10 w-10 rounded-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FBE3E0]/20 text-sm font-bold text-[#FBE3E0]"
                                        >
                                            {{ scoreEntry.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-[#FBE3E0]">{{ scoreEntry.user.name }}</div>
                                            <div v-if="scoreEntry.user.team_name" class="text-xs text-[#FBE3E0]/60">
                                                {{ scoreEntry.user.team_name }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xl font-black text-[#FBE3E0]">{{ scoreEntry.score }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Reactions & Comments -->
                <div class="flex flex-col gap-6">
                    <!-- Reactions -->
                    <Card class="border-none bg-[#677D74] shadow-lg">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                                <Smile class="h-5 w-5" />
                                Reactions
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-wrap gap-2">
                                <!-- Existing reactions -->
                                <button
                                    v-for="reaction in reactions"
                                    :key="reaction.emoji"
                                    @click="toggleEmoji(reaction.emoji)"
                                    class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm transition-colors"
                                    :class="
                                        reaction.reacted
                                            ? 'border-[#FBE3E0] bg-[#FBE3E0] text-[#677D74]'
                                            : 'border-[#FBE3E0]/30 text-[#FBE3E0] hover:bg-[#FBE3E0] hover:text-[#677D74]'
                                    "
                                >
                                    <span>{{ reaction.emoji }}</span>
                                    <span class="font-medium">{{ reaction.count }}</span>
                                </button>

                                <!-- Available emojis to add -->
                                <button
                                    v-for="emoji in availableEmojis.filter(
                                        (e) => !reactions.find((r) => r.emoji === e),
                                    )"
                                    :key="emoji"
                                    @click="toggleEmoji(emoji)"
                                    class="inline-flex items-center rounded-full border border-dashed border-[#FBE3E0]/20 px-3 py-1.5 text-sm text-[#FBE3E0]/50 transition-colors hover:border-[#FBE3E0]/50 hover:text-[#FBE3E0]"
                                >
                                    {{ emoji }}
                                </button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Comments -->
                    <Card class="border-none bg-[#677D74] shadow-lg">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2 text-[#FBE3E0]">
                                <MessageCircle class="h-5 w-5" />
                                Comments
                                <span class="text-sm font-normal text-[#FBE3E0]/60">({{ comments.length }})</span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <!-- Comment form -->
                            <form @submit.prevent="submitComment" class="mb-4 flex gap-2">
                                <Input
                                    v-model="commentForm.body"
                                    placeholder="Write a comment..."
                                    class="flex-1 border-[#FBE3E0]/30 bg-[#677D74] text-[#FBE3E0] placeholder:text-[#FBE3E0]/40"
                                    required
                                />
                                <Button
                                    type="submit"
                                    size="icon"
                                    :disabled="commentForm.processing || !commentForm.body.trim()"
                                    class="shrink-0 rounded-full bg-[#FBE3E0] text-[#677D74] hover:bg-[#677D74] hover:text-[#FBE3E0] hover:ring-1 hover:ring-[#FBE3E0]"
                                >
                                    <Send class="h-4 w-4" />
                                </Button>
                            </form>
                            <InputError class="mb-2" :message="commentForm.errors.body" />

                            <Separator class="mb-4 bg-[#FBE3E0]/20" />

                            <!-- Comments list -->
                            <div v-if="comments.length === 0" class="py-4 text-center text-sm text-[#FBE3E0]/60">
                                No comments yet. Start the conversation!
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="comment in comments"
                                    :key="comment.id"
                                    class="group rounded-lg border border-[#FBE3E0]/10 p-3"
                                >
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <img
                                                v-if="comment.user.profile_picture"
                                                :src="comment.user.profile_picture"
                                                :alt="comment.user.name"
                                                class="h-8 w-8 rounded-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#FBE3E0]/20 text-xs font-bold text-[#FBE3E0]"
                                            >
                                                {{ comment.user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <span class="text-sm font-semibold text-[#FBE3E0]">{{ comment.user.name }}</span>
                                            <span class="text-xs text-[#FBE3E0]/50">{{ formatCommentDate(comment.created_at) }}</span>
                                        </div>
                                        <button
                                            v-if="comment.user.id === currentUserId"
                                            @click="deleteCommentById(comment.id)"
                                            class="hidden rounded p-1 text-red-400/70 transition-colors hover:bg-red-400/10 hover:text-red-400 group-hover:block"
                                        >
                                            <Trash2 class="h-3 w-3" />
                                        </button>
                                    </div>
                                    <p class="mt-1.5 pl-10 text-sm text-[#FBE3E0]/90">{{ comment.body }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
