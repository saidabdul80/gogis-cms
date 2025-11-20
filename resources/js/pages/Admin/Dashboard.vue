<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { computed } from 'vue'
import { Bar, Doughnut, Line } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PointElement,
    LineElement,
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PointElement,
    LineElement
)

interface Analytics {
    overview: {
        totalCustomers: number
        totalIndividualCustomers: number
        totalCorporateCustomers: number
        totalProperties: number
        totalInvoices: number
        totalRevenue: number
        paidInvoices: number
        pendingInvoices: number
        partialInvoices: number
        overdueInvoices: number
    }
    revenue: {
        totalInvoiceAmount: number
        totalPaidAmount: number
        totalOutstanding: number
        collectionRate: number
    }
    charts: {
        monthlyRevenue: Array<{ month: string; total: number }>
        paymentStatusDistribution: Array<{ status: string; count: number }>
        propertiesByType: Array<{ type: string; count: number }>
        propertiesByLga: Array<{ lga: string; count: number }>
    }
    recent: {
        invoices: Array<any>
        payments: Array<any>
        customers: Array<any>
    }
}

interface Props {
    analytics: Analytics
}

const props = defineProps<Props>()

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 2,
    }).format(amount)
}

const formatNumber = (num: number) => {
    return new Intl.NumberFormat('en-NG').format(num)
}

// Monthly Revenue Chart Data
const monthlyRevenueChartData = computed(() => ({
    labels: props.analytics.charts.monthlyRevenue.map((item) => item.month),
    datasets: [
        {
            label: 'Revenue',
            data: props.analytics.charts.monthlyRevenue.map((item) => item.total),
            backgroundColor: '#1c6434',
            borderColor: '#1c6434',
            borderWidth: 2,
            tension: 0.4,
        },
    ],
}))

const monthlyRevenueChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            callbacks: {
                label: (context: any) => {
                    return 'Revenue: ' + formatCurrency(context.parsed.y)
                },
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: any) => '₦' + formatNumber(value),
            },
        },
    },
}

// Payment Status Distribution Chart Data
const paymentStatusChartData = computed(() => ({
    labels: props.analytics.charts.paymentStatusDistribution.map((item) => item.status),
    datasets: [
        {
            data: props.analytics.charts.paymentStatusDistribution.map((item) => item.count),
            backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
            borderWidth: 0,
        },
    ],
}))

const paymentStatusChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
    },
}

// Properties by Type Chart Data
const propertiesByTypeChartData = computed(() => ({
    labels: props.analytics.charts.propertiesByType.map((item) => item.type),
    datasets: [
        {
            label: 'Properties',
            data: props.analytics.charts.propertiesByType.map((item) => item.count),
            backgroundColor: '#fecd07',
            borderColor: '#c39913',
            borderWidth: 1,
        },
    ],
}))

const propertiesByTypeChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            },
        },
    },
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <v-container fluid>
            <!-- Welcome Header -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <h1 class="text-h4 font-weight-bold">Welcome back, {{ $page.props.auth.user?.first_name }}!</h1>
                    <p class="text-subtitle-1 text-grey">Here's what's happening with your property management system</p>
                </v-col>
            </v-row>

            <!-- Overview Stats Cards -->
            <v-row class="mb-6">
                <v-col cols="12" sm="6" md="3">
                    <v-card :color="$page.props.appSettings.primaryColor" class="stat-card">
                        <v-card-text>
                            <div class="text-white">
                                <v-icon size="40" class="mb-2">mdi-account-group</v-icon>
                                <div class="text-h4 font-weight-bold">{{ formatNumber(analytics.overview.totalCustomers) }}</div>
                                <div class="text-body-1">Total Customers</div>
                                <div class="text-caption mt-2">
                                    {{ formatNumber(analytics.overview.totalIndividualCustomers) }} Individual |
                                    {{ formatNumber(analytics.overview.totalCorporateCustomers) }} Corporate
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card color="info" class="stat-card">
                        <v-card-text>
                            <div class="text-white">
                                <v-icon size="40" class="mb-2">mdi-home-city</v-icon>
                                <div class="text-h4 font-weight-bold">{{ formatNumber(analytics.overview.totalProperties) }}</div>
                                <div class="text-body-1">Total Properties</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card :color="$page.props.appSettings.secondaryColor" class="stat-card">
                        <v-card-text>
                            <div style="color: #000;">
                                <v-icon size="40" class="mb-2">mdi-file-document</v-icon>
                                <div class="text-h4 font-weight-bold">{{ formatNumber(analytics.overview.totalInvoices) }}</div>
                                <div class="text-body-1">Total Invoices</div>
                                <div class="text-caption mt-2">
                                    {{ formatNumber(analytics.overview.paidInvoices) }} Paid |
                                    {{ formatNumber(analytics.overview.pendingInvoices) }} Pending
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card :color="$page.props.appSettings.accentColor" class="stat-card">
                        <v-card-text>
                            <div class="text-white">
                                <v-icon size="40" class="mb-2">mdi-cash-multiple</v-icon>
                                <div class="text-h4 font-weight-bold">{{ formatCurrency(analytics.overview.totalRevenue) }}</div>
                                <div class="text-body-1">Total Revenue</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Revenue & Invoice Stats -->
            <v-row class="mb-6">
                <v-col cols="12" sm="6" md="3">
                    <v-card class="stat-card-secondary">
                        <v-card-text>
                            <div class="d-flex align-center">
                                <v-icon size="30" color="success" class="mr-3">mdi-check-circle</v-icon>
                                <div>
                                    <div class="text-h6 font-weight-bold">{{ formatNumber(analytics.overview.paidInvoices) }}</div>
                                    <div class="text-caption text-grey">Paid Invoices</div>
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card class="stat-card-secondary">
                        <v-card-text>
                            <div class="d-flex align-center">
                                <v-icon size="30" color="warning" class="mr-3">mdi-clock-outline</v-icon>
                                <div>
                                    <div class="text-h6 font-weight-bold">{{ formatNumber(analytics.overview.pendingInvoices) }}</div>
                                    <div class="text-caption text-grey">Pending Invoices</div>
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card class="stat-card-secondary">
                        <v-card-text>
                            <div class="d-flex align-center">
                                <v-icon size="30" color="orange" class="mr-3">mdi-alert-circle</v-icon>
                                <div>
                                    <div class="text-h6 font-weight-bold">{{ formatNumber(analytics.overview.overdueInvoices) }}</div>
                                    <div class="text-caption text-grey">Overdue Invoices</div>
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card class="stat-card-secondary">
                        <v-card-text>
                            <div class="d-flex align-center">
                                <v-icon size="30" color="primary" class="mr-3">mdi-percent</v-icon>
                                <div>
                                    <div class="text-h6 font-weight-bold">{{ analytics.revenue.collectionRate }}%</div>
                                    <div class="text-caption text-grey">Collection Rate</div>
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Revenue Summary -->
            <v-row class="mb-6">
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="bg-primary text-white">
                            <v-icon class="mr-2">mdi-chart-line</v-icon>
                            Revenue Summary
                        </v-card-title>
                        <v-card-text class="pa-6">
                            <v-row>
                                <v-col cols="12" md="4">
                                    <div class="text-center">
                                        <div class="text-h5 font-weight-bold text-primary">{{ formatCurrency(analytics.revenue.totalInvoiceAmount) }}</div>
                                        <div class="text-body-2 text-grey">Total Invoice Amount</div>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-center">
                                        <div class="text-h5 font-weight-bold text-success">{{ formatCurrency(analytics.revenue.totalPaidAmount) }}</div>
                                        <div class="text-body-2 text-grey">Total Paid Amount</div>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <div class="text-center">
                                        <div class="text-h5 font-weight-bold text-warning">{{ formatCurrency(analytics.revenue.totalOutstanding) }}</div>
                                        <div class="text-body-2 text-grey">Total Outstanding</div>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Charts Row -->
            <v-row class="mb-6">
                <!-- Monthly Revenue Trend -->
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-title class="bg-primary text-white">
                            <v-icon class="mr-2">mdi-chart-line</v-icon>
                            Monthly Revenue Trend (Last 12 Months)
                        </v-card-title>
                        <v-card-text class="pa-4">
                            <div style="height: 300px;">
                                <Line :data="monthlyRevenueChartData" :options="monthlyRevenueChartOptions" />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Payment Status Distribution -->
                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-title class="bg-primary text-white">
                            <v-icon class="mr-2">mdi-chart-donut</v-icon>
                            Payment Status
                        </v-card-title>
                        <v-card-text class="pa-4">
                            <div style="height: 300px;">
                                <Doughnut :data="paymentStatusChartData" :options="paymentStatusChartOptions" />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Properties Analytics -->
            <v-row class="mb-6">
                <!-- Properties by Type -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="bg-secondary">
                            <v-icon class="mr-2">mdi-home-group</v-icon>
                            Properties by Type
                        </v-card-title>
                        <v-card-text class="pa-4">
                            <div style="height: 300px;">
                                <Bar :data="propertiesByTypeChartData" :options="propertiesByTypeChartOptions" />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Properties by LGA -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="bg-secondary">
                            <v-icon class="mr-2">mdi-map-marker-multiple</v-icon>
                            Properties by LGA (Top 10)
                        </v-card-title>
                        <v-card-text class="pa-4">
                            <v-list density="compact">
                                <v-list-item
                                    v-for="(item, index) in analytics.charts.propertiesByLga"
                                    :key="index"
                                    class="mb-2"
                                >
                                    <template #prepend>
                                        <v-chip size="small" color="primary" class="mr-2">{{ index + 1 }}</v-chip>
                                    </template>
                                    <v-list-item-title>{{ item.lga }}</v-list-item-title>
                                    <template #append>
                                        <v-chip size="small" color="accent">{{ item.count }} properties</v-chip>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Recent Activities -->
            <v-row>
                <!-- Recent Invoices -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="bg-info text-white">
                            <v-icon class="mr-2">mdi-file-document-multiple</v-icon>
                            Recent Invoices
                        </v-card-title>
                        <v-card-text class="pa-0">
                            <v-list density="compact">
                                <v-list-item
                                    v-for="invoice in analytics.recent.invoices"
                                    :key="invoice.id"
                                    :to="`/admin/invoices/${invoice.id}`"
                                >
                                    <v-list-item-title class="font-weight-medium">{{ invoice.invoice_number }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ invoice.customer_name }} - {{ invoice.property }}
                                    </v-list-item-subtitle>
                                    <template #append>
                                        <div class="text-right">
                                            <div class="font-weight-bold">{{ formatCurrency(invoice.amount) }}</div>
                                            <v-chip
                                                size="x-small"
                                                :color="invoice.payment_status === 'PAID' ? 'success' : invoice.payment_status === 'PARTIAL' ? 'warning' : 'error'"
                                            >
                                                {{ invoice.payment_status }}
                                            </v-chip>
                                        </div>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Recent Payments -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title class="bg-success text-white">
                            <v-icon class="mr-2">mdi-cash-check</v-icon>
                            Recent Payments
                        </v-card-title>
                        <v-card-text class="pa-0">
                            <v-list density="compact">
                                <v-list-item
                                    v-for="payment in analytics.recent.payments"
                                    :key="payment.id"
                                >
                                    <v-list-item-title class="font-weight-medium">{{ payment.reference }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ payment.customer_name }} - {{ payment.invoice_number }}
                                    </v-list-item-subtitle>
                                    <template #append>
                                        <div class="text-right">
                                            <div class="font-weight-bold">{{ formatCurrency(payment.amount) }}</div>
                                            <v-chip
                                                size="x-small"
                                                :color="payment.status === 'SUCCESS' ? 'success' : payment.status === 'PENDING' ? 'warning' : 'error'"
                                            >
                                                {{ payment.status }}
                                            </v-chip>
                                        </div>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Recent Customers -->
            <v-row class="mt-6">
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="bg-accent text-white">
                            <v-icon class="mr-2">mdi-account-multiple-plus</v-icon>
                            Recent Customers
                        </v-card-title>
                        <v-card-text class="pa-0">
                            <v-data-table
                                :headers="[
                                    { title: 'Type', key: 'type' },
                                    { title: 'Name', key: 'name' },
                                    { title: 'Email', key: 'email' },
                                    { title: 'Phone', key: 'phone' },
                                    { title: 'Registered', key: 'created_at' },
                                ]"
                                :items="analytics.recent.customers"
                                :items-per-page="10"
                                density="compact"
                            >
                                <template #item.type="{ item }">
                                    <v-chip
                                        size="x-small"
                                        :color="(item as any).type === 'Individual' ? 'primary' : 'secondary'"
                                    >
                                        {{ (item as any).type }}
                                    </v-chip>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<style scoped>
.stat-card {
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.stat-card-secondary {
    transition: transform 0.2s;
    border-left: 4px solid #1c6434;
}

.stat-card-secondary:hover {
    transform: translateY(-2px);
}
</style>

