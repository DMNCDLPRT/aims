<script setup lang="ts">
import Icon from './Icon.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [Number, String],
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    trend: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="rounded-xl border bg-card text-card-foreground shadow">
        <!-- Header -->
        <div class="flex flex-row items-center justify-between p-6 pb-2">
            <h3 class="text-sm font-medium tracking-tight">{{ title }}</h3>
            <Icon :name="icon" class="h-5 w-5 text-muted-foreground" />
        </div>

        <!-- Value -->
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold">
                {{ typeof value === 'number' ? value.toLocaleString() : value }}
            </div>
            <p v-if="description" class="text-xs text-muted-foreground">
                {{ description }}
            </p>
            <p v-if="trend" class="text-xs font-medium text-green-600">
                {{ trend }}
            </p>
        </div>
    </div>
</template>
