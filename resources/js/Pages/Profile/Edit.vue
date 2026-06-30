<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const Layout = computed(() => page.props.auth.user?.is_admin ? AdminLayout : UserLayout);
</script>

<template>
    <Head title="Profile" />

    <component :is="Layout">
        <template #header v-if="page.props.auth.user?.is_admin">
            <h2 class="text-2xl font-serif font-medium leading-tight text-ink tracking-tight">
                Profile Settings
            </h2>
        </template>

        <div class="py-16">
            <div class="mx-auto max-w-4xl space-y-10 px-4 sm:px-6 lg:px-8">
                <!-- Profile Information Card -->
                <div class="bg-paper p-8 border border-oat-dark sm:rounded-2xl shadow-elevated">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Update Password Card -->
                <div class="bg-paper p-8 border border-oat-dark sm:rounded-2xl shadow-elevated">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Delete User Card -->
                <div class="bg-paper p-8 border border-oat-dark sm:rounded-2xl shadow-elevated">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </component>
</template>
