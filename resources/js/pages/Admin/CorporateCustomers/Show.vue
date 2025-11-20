<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

interface PropertyType {
    id: number
    name: string
}

interface Property {
    id: number
    number_type: string
    number_value: string
    address: string
    property_type: PropertyType | null
    amount: number
    payment_frequency: string
    start_date: string
    last_invoice_due_date: string | null
    last_invoice_status: string | null
}

interface Customer {
    id: number
    company_name: string
    category?: string
    email?: string
    phone_number?: string
    address?: string
}

interface Analytics {
    total_properties: number
    total_paid_amount: number
    properties_by_type: Record<string, { count: number; total_amount: number }>
}

interface Props {
    customer: Customer
    properties: Property[]
    analytics: Analytics
}

const props = defineProps<Props>()

const selectedPropertyId = ref<number | null>(props.properties[0]?.id || null)
const paymentHistory = ref<any[]>([])
const loadingPayments = ref(false)

const companyName = computed(() => {
    return props.customer.company_name
})

const selectedProperty = computed(() => {
    return props.properties.find(p => p.id === selectedPropertyId.value)
})

// Fetch payment history when property is selected
const fetchPaymentHistory = async (propertyId: number) => {
    loadingPayments.value = true
    try {
        const response = await fetch(route('admin.corporate-customers.property-payments', {
            corporateCustomer: props.customer.id,
            property_id: propertyId
        }))
        paymentHistory.value = await response.json()
    } catch (error) {
        console.error('Error fetching payment history:', error)
    } finally {
        loadingPayments.value = false
    }
}

// Watch for property selection changes
const handlePropertySelect = (propertyId: number) => {
    selectedPropertyId.value = propertyId
    fetchPaymentHistory(propertyId)
}

// Load initial payment history
if (selectedPropertyId.value) {
    fetchPaymentHistory(selectedPropertyId.value)
}

// Chart data
const chartData = computed(() => {
    if (!paymentHistory.value.length) {
        return {
            labels: [],
            datasets: []
        }
    }

    return {
        labels: paymentHistory.value.map(p => new Date(p.payment_date).toLocaleDateString()),
        datasets: [
            {
                label: 'Payment Amount (₦)',
                data: paymentHistory.value.map(p => p.amount),
                backgroundColor: 'rgba(28, 100, 52, 0.8)',
                borderColor: 'rgba(28, 100, 52, 1)',
                borderWidth: 1
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top' as const
        },
        title: {
            display: true,
            text: `Last 5 Payments - ${selectedProperty.value?.payment_frequency || ''}`
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value: any) {
                    return '₦' + value.toLocaleString()
                }
            }
        }
    }
}

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN'
    }).format(amount)
}

const formatPaymentFrequency = (frequency?: string) => {
    if (!frequency) return 'N/A'
    return frequency.charAt(0).toUpperCase() + frequency.slice(1)
}

const getStatusColor = (status?: string) => {
    if (!status) return 'grey'
    switch (status) {
        case 'PAID': return 'success'
        case 'PENDING': return 'warning'
        case 'PARTIAL': return 'info'
        case 'FAILED': return 'error'
        case 'CANCELLED': return 'grey'
        default: return 'grey'
    }
}


</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <!-- Header -->
            <v-row class="mb-4">
                <v-col cols="12">
                    <div class="d-flex justify-space-between align-center">
                        <div>
                            <h1 class="text-h4 font-weight-bold">{{ companyName }}</h1>
                            <p class="text-subtitle-1 text-grey">Corporate Customer Profile</p>
                        </div>
                        <div>
                            <Link :href="route('admin.corporate-customers.edit', customer.id)">
                                <v-btn color="primary" prepend-icon="mdi-pencil">
                                    Edit Customer
                                </v-btn>
                            </Link>
                            <Link :href="route('admin.corporate-customers.index')" class="ml-2">
                                <v-btn variant="outlined">
                                    Back to List
                                </v-btn>
                            </Link>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <!-- Analytics Summary Cards -->
            <v-row class="mb-4">
                <v-col cols="12" md="4">
                    <v-card color="primary" dark>
                        <v-card-text >
                            <div class="text-overline text-white">Total Properties</div>
                            <div class="text-h3 font-weight-bold text-white">{{ analytics.total_properties }}</div>
                            <v-icon size="48" class="float-right mt-n8 text-white">mdi-home-city</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="4">
                    <v-card color="success" dark>
                        <v-card-text>
                            <div class="text-overline text-white">Total Paid Amount</div>
                            <div class="text-h3 font-weight-bold text-white">{{ formatCurrency(analytics.total_paid_amount) }}</div>
                            <v-icon size="48" class="float-right mt-n8 text-white">mdi-cash-multiple</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" md="4">
                    <v-card color="info" dark>
                        <v-card-text>
                            <div class="text-overline text-white">Property Types</div>
                            <div class="text-h3 font-weight-bold text-white">{{ Object.keys(analytics.properties_by_type).length }}</div>
                            <v-icon size="48" class="float-right mt-n8 text-white">mdi-shape</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row>
                <!-- Customer Information -->
                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-title>Customer Information</v-card-title>
                        <v-card-text>
                            <v-list density="compact">
                                <v-list-item>
                                    <template #prepend>
                                        <v-icon>mdi-office-building</v-icon>
                                    </template>
                                    <v-list-item-title>Company Name</v-list-item-title>
                                    <v-list-item-subtitle>{{ companyName }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item v-if="customer.category">
                                    <template #prepend>
                                        <v-icon>mdi-tag</v-icon>
                                    </template>
                                    <v-list-item-title>Category</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.category }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item v-if="customer.email">
                                    <template #prepend>
                                        <v-icon>mdi-email</v-icon>
                                    </template>
                                    <v-list-item-title>Email</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.email }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item v-if="customer.phone_number">
                                    <template #prepend>
                                        <v-icon>mdi-phone</v-icon>
                                    </template>
                                    <v-list-item-title>Phone</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.phone_number }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item v-if="customer.address">
                                    <template #prepend>
                                        <v-icon>mdi-home</v-icon>
                                    </template>
                                    <v-list-item-title>Address</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.address }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>

                    <!-- Properties by Type -->
                    <v-card class="mt-4">
                        <v-card-title>Properties by Type</v-card-title>
                        <v-card-text>
                            <v-list density="compact">
                                <v-list-item
                                    v-for="(data, typeName) in analytics.properties_by_type"
                                    :key="typeName"
                                >
                                    <v-list-item-title>{{ typeName }}</v-list-item-title>
                                    <template #append>
                                        <v-chip size="small" color="primary">{{ data.count }}</v-chip>
                                    </template>
                                    <v-list-item-subtitle>
                                        Total: {{ formatCurrency(data.total_amount) }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item v-if="Object.keys(analytics.properties_by_type).length === 0">
                                    <v-list-item-title class="text-grey">No properties yet</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Properties List and Payment Chart -->
                <v-col cols="12" md="8">
                    <!-- Properties List -->
                    <v-card class="mb-4">
                        <v-card-title>Properties & Last Invoice Due Date</v-card-title>
                        <v-card-text>
                            <v-data-table
                                :headers="[
                                    { title: 'Property Number', key: 'number_value' },
                                    { title: 'Type', key: 'property_type.name' },
                                    { title: 'Address', key: 'address' },
                                    { title: 'Amount', key: 'amount' },
                                    { title: 'Frequency', key: 'payment_frequency' },
                                    { title: 'Last Invoice Due', key: 'last_invoice_due_date' },
                                    { title: 'Status', key: 'last_invoice_status' },
                                ]"
                                :items="properties"
                                :items-per-page="10"
                                class="elevation-0"
                                @click:row="(event, { item }) => handlePropertySelect(item.id)"
                                hover
                            >
                                <template #item.number_value="{ item }">
                                    <strong>{{ item.number_type }}-{{ item.number_value }}</strong>
                                </template>
                                <template #item.amount="{ item }">
                                    {{ formatCurrency(item.amount) }}
                                </template>
                                <template #item.payment_frequency="{ item }">
                                    <v-chip size="small" color="info">
                                        {{ formatPaymentFrequency(item.payment_frequency) }}
                                    </v-chip>
                                </template>
                                <template #item.last_invoice_due_date="{ item }">
                                    <span v-if="item.last_invoice_due_date">
                                        {{ new Date(item.last_invoice_due_date).toLocaleDateString() }}
                                    </span>
                                    <span v-else class="text-grey">No invoice</span>
                                </template>
                                <template #item.last_invoice_status="{ item }">
                                    <v-chip
                                        v-if="item.last_invoice_status"
                                        size="small"
                                        :color="getStatusColor(item.last_invoice_status)"
                                    >
                                        {{ item.last_invoice_status }}
                                    </v-chip>
                                    <span v-else class="text-grey">-</span>
                                </template>
                                <template #no-data>
                                    <v-alert type="info" variant="tonal" class="my-4">
                                        No properties found for this customer.
                                    </v-alert>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>

                    <!-- Payment History Chart -->
                    <v-card>
                        <v-card-title>
                            <div class="d-flex justify-space-between align-center">
                                <span>Payment History</span>
                                <v-chip v-if="selectedProperty" color="primary" size="small">
                                    {{ selectedProperty.number_type }}-{{ selectedProperty.number_value }}
                                </v-chip>
                            </div>
                        </v-card-title>
                        <v-card-text>
                            <div v-if="loadingPayments" class="text-center py-8">
                                <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                <p class="mt-4 text-grey">Loading payment history...</p>
                            </div>
                            <div v-else-if="!selectedPropertyId" class="text-center py-8">
                                <v-icon size="64" color="grey">mdi-chart-bar</v-icon>
                                <p class="mt-4 text-grey">Select a property to view payment history</p>
                            </div>
                            <div v-else-if="paymentHistory.length === 0" class="text-center py-8">
                                <v-icon size="64" color="grey">mdi-cash-remove</v-icon>
                                <p class="mt-4 text-grey">No payment history for this property</p>
                            </div>
                            <div v-else style="height: 400px;">
                                <Bar :data="chartData" :options="chartOptions" />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>
