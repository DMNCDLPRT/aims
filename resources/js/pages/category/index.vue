<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import categories from '@/routes/categories';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: categories.index().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <h1 class="text-2xl font-bold">Category Page</h1>
            <p class="max-w-3xl">
                This is a category page. You can add your category related
                content here.
            </p>
        </div>
    </AppLayout>
</template>
