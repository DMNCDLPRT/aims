<script setup lang="ts">
import CardStats from '@/components/CardStats.vue';
import BarChart from '@/components/chartcomponents/BarChart.vue';
import DoughnutChart from '@/components/chartcomponents/DoughnutChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const totals = ref<any>({
    total_assets: 0,
    total_categories: 0,
    total_manufacturers: 0,
    total_locations: 0,
    total_users: 0,
});

const charts = ref<any>({
    assets_by_status: [],
    assets_by_category: [],
    assets_by_location: [],
    assets_by_assigned_user: [],
});

const loading = ref(true);

// Fetch Dashboard Data
const fetchStats = async () => {
    try {
        const response = await axios.get('/dashboard/stats');
        totals.value = response.data.totals;
        charts.value = response.data.charts;
    } catch (error) {
        console.error('Error fetching dashboard stats:', error);
    }
};

const chartColors = [
    '#3B82F6', // blue-500
    '#10B981', // emerald-500
    '#F59E0B', // amber-500
    '#EF4444', // red-500
    '#8B5CF6', // violet-500
    '#06B6D4', // cyan-500
    '#F43F5E', // rose-500
    '#84CC16', // lime-500
    '#6366F1', // indigo-500
    '#14B8A6', // teal-500
];

// Pick color by index
const getColor = (index: number) => chartColors[index % chartColors.length];

// Chart Refs
const chartData = ref<any>(null);
const chartOptions = ref<any>(null);
const piechartData = ref<any>(null);
const piechartOptions = ref<any>(null);

const isDark = document.documentElement.classList.contains('dark');

const renderCharts = () => {
    // Bar Chart: Assets by Status
    chartData.value = {
        labels:
            charts.value.assets_by_status?.map((item: any) => item.status) ||
            [],
        datasets: [
            {
                label: 'Assets by Status',
                backgroundColor:
                    charts.value.assets_by_status?.map((_: any, i: number) =>
                        getColor(i),
                    ) || [],
                data:
                    charts.value.assets_by_status?.map(
                        (item: any) => item.total,
                    ) || [],
            },
        ],
    };

    chartOptions.value = {
        responsive: true,
        plugins: {
            legend: { display: true, position: 'top' },
            title: { display: true, text: 'Total Assets by Status' },
        },
        borderColor: isDark ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)',
        borderWidth: 1,
        tooltip: {
            legend: { display: true, position: 'bottom' },
            bodyColor: isDark ? '#ffffff' : '#111827',
            titleColor: isDark ? '#ffffff' : '#111827',
        },
    };

    // Pie Chart: Assets by Category
    piechartData.value = {
        labels:
            charts.value.assets_by_category?.map(
                (item: any) => item.category.name,
            ) || [],
        datasets: [
            {
                label: 'Assets by Category',
                backgroundColor:
                    charts.value.assets_by_category?.map((_: any, i: number) =>
                        getColor(i),
                    ) || [],
                data:
                    charts.value.assets_by_category?.map(
                        (item: any) => item.total,
                    ) || [],
            },
        ],
    };

    piechartOptions.value = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'left' },
            title: { display: true, text: 'Total Assets by Category' },
        },
    };
};

// Lifecycle
onMounted(async () => {
    loading.value = true;
    await fetchStats();
    renderCharts();
    loading.value = false;
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 overflow-y-auto p-4">
            <!-- Stat Cards -->
            <div class="grid gap-4 md:grid-cols-5">
                <CardStats
                    title="Total Assets"
                    :value="totals.total_assets"
                    icon="list"
                />
                <CardStats
                    title="Total Categories"
                    :value="totals.total_categories"
                    icon="folder"
                />
                <CardStats
                    title="Total Manufacturers"
                    :value="totals.total_manufacturers"
                    icon="factory"
                />
                <CardStats
                    title="Total Locations"
                    :value="totals.total_locations"
                    icon="map-pin"
                />
                <CardStats
                    title="Total Users"
                    :value="totals.total_users"
                    icon="users"
                />
            </div>

            <!-- Charts -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Pie Chart -->
                <div
                    class="h-[450px] rounded-xl border bg-card p-6 text-card-foreground shadow"
                >
                    <div
                        v-if="loading"
                        class="flex h-full items-center justify-center"
                    >
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-blue-500"
                        ></div>
                    </div>
                    <div v-else class="h-full">
                        <DoughnutChart
                            icon="ChartPie"
                            :title="'Assets by Category'"
                            :chart-data="piechartData"
                            :chart-options="piechartOptions"
                        />
                    </div>
                </div>

                <!-- Bar Chart -->
                <div
                    class="h-[450px] rounded-xl border bg-card p-6 text-card-foreground shadow"
                >
                    <div
                        v-if="loading"
                        class="flex h-full items-center justify-center"
                    >
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-blue-500"
                        ></div>
                    </div>
                    <div v-else class="h-full">
                        <BarChart
                            icon="ChartColumn"
                            :title="'Assets by Status'"
                            :chart-data="chartData"
                            :chart-options="chartOptions"
                        />
                    </div>
                </div>

                <!-- Bar Chart -->
                <div
                    class="h-[450px] rounded-xl border bg-card p-6 text-card-foreground shadow"
                >
                    <div
                        v-if="loading"
                        class="flex h-full items-center justify-center"
                    >
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-blue-500"
                        ></div>
                    </div>
                    <div v-else class="h-full">
                        <BarChart
                            icon="ChartColumn"
                            :title="'Assets by Status'"
                            :chart-data="chartData"
                            :chart-options="chartOptions"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
