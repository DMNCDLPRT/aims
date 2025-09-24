<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import locations from '@/routes/locations';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Location',
        href: locations.index().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl font-bold">Locations Page</h1>
            <p class="max-w-3xl">
                This is a locations page. You can add your locations related
                content here.
            </p>
        </div>
    </AppLayout>
</template>
