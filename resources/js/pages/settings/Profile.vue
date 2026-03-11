<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import { update as profileUpdate } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { BreadcrumbItem } from '@/types';
import { Camera } from 'lucide-vue-next';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit(),
    },
];

const page = usePage();
const user = computed(() => page.props.auth.user);

const form = useForm({
    _method: 'patch' as const,
    name: user.value.name,
    email: user.value.email,
    team_name: (user.value as Record<string, unknown>).team_name as string ?? '',
    profile_picture: null as File | null,
});

const recentlySuccessful = ref(false);
const profilePicturePreview = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

function onProfilePictureChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.profile_picture = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            profilePicturePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function triggerFileInput() {
    fileInputRef.value?.click();
}

const currentProfilePicture = computed(() => {
    return (user.value as Record<string, unknown>).profile_picture as string | null;
});

function submit() {
    form.post(profileUpdate.url(), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            recentlySuccessful.value = true;
            profilePicturePreview.value = null;
            form.profile_picture = null;
            setTimeout(() => {
                recentlySuccessful.value = false;
            }, 2000);
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <h1 class="sr-only">Profile settings</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Profile information"
                    description="Update your name, email address, team name, and profile picture"
                />

                <form
                    @submit.prevent="submit"
                    class="space-y-6"
                >
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            class="mt-1 block w-full"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="team_name">Team Name</Label>
                        <Input
                            id="team_name"
                            v-model="form.team_name"
                            class="mt-1 block w-full"
                            autocomplete="organization"
                            placeholder="Your team name (optional)"
                        />
                        <InputError class="mt-2" :message="form.errors.team_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Profile Picture</Label>
                        <div class="mt-1 flex items-center gap-4">
                            <div class="relative">
                                <img
                                    v-if="profilePicturePreview || currentProfilePicture"
                                    :src="profilePicturePreview ?? currentProfilePicture ?? ''"
                                    alt="Profile picture"
                                    class="h-16 w-16 rounded-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-muted text-xl font-bold text-muted-foreground"
                                >
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <button
                                    type="button"
                                    @click="triggerFileInput"
                                    class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 transition-opacity hover:opacity-100"
                                >
                                    <Camera class="h-5 w-5 text-white" />
                                </button>
                            </div>
                            <div>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="triggerFileInput"
                                >
                                    Choose Photo
                                </Button>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    JPG, PNG, or GIF. Max 2MB.
                                </p>
                            </div>
                            <input
                                ref="fileInputRef"
                                type="file"
                                accept="image/jpeg,image/png,image/gif"
                                class="hidden"
                                @change="onProfilePictureChange"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.profile_picture" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            A new verification link has been sent to your email
                            address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="form.processing"
                            data-test="update-profile-button"
                            >Save</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
