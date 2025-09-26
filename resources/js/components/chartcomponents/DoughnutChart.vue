<script setup lang="ts">
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { Doughnut } from 'vue-chartjs';
import Icon from '../Icon.vue';

// Register Chart.js components
ChartJS.register(ArcElement, Tooltip, Legend, ChartDataLabels);

defineProps({
    chartData: {
        type: Object as () => any,
        required: true,
    },
    chartOptions: {
        type: Object as () => any,
        required: true,
    },
    title: {
        type: String,
        required: false,
        default: '',
    },
    icon: {
        type: String,
        required: false,
        default: 'chart',
    },
});
</script>
<template>
    <div>
        <div class="flex flex-row items-center pb-4 gap-2">
            <Icon :name="icon" class="h-5 w-5 text-muted-foreground items-center" />
            <h3 class="leading-none font-semibold tracking-tight">{{ title }}</h3>
        </div>
        <div>
            <Doughnut :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
