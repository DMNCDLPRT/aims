<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { about } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'About Us',
        href: about().url,
    },
];
</script>

<template>
    <Head title="About Us" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl font-bold">About Us</h1>
            <p class="max-w-3xl">
                This is anonymous group official website control by anonymous
                headquarters. Here you can read the latest news about anonymous.
            </p>
            <p class="max-w-3xl">
                Anonymous is a decentralised virtual community. They are
                commonly referred to as an internet-based collective of
                hacktivists whose goals, like its organization, are
                decentralized. Anonymous seeks mass awareness and revolution
                against what the group perceives as corrupt entities, while
                attempting to maintain anonymity.
            </p>
        </div>
    </AppLayout>
</template>
