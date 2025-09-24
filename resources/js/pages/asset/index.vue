<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import assets from '@/routes/assets';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Asset',
        href: assets.index().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl font-bold">Asset Page</h1>
            <p class="max-w-3xl">
                This is an asset page. You can add your asset related
                content here.
            </p>
        </div>
    </AppLayout>
</template>
