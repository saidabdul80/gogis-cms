<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Invoice Management</span>
                            <v-btn
                                color="primary"
                                prepend-icon="mdi-plus"
                                @click="$inertia.visit(route('admin.invoices.create'))"
                            >
                                Create Invoice
                            </v-btn>
                        </v-card-title>

                        <v-card-text>
                            <!-- Filters -->
                            <v-row class="mb-4">
                                <v-col cols="12" md="4">
                                    <v-text-field
                                        v-model="search"
                                        label="Search"
                                        prepend-inner-icon="mdi-magnify"
                                        variant="outlined"
                                        density="compact"
                                        clearable
                                        @update:model-value="debouncedSearch"
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-select
                                        v-model="paymentStatus"
                                        label="Payment Status"
                                        :items="statusOptions"
                                        variant="outlined"
                                        density="compact"
                                        clearable
                                        @update:model-value="applyFilters"
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-select
                                        v-model="propertyType"
                                        label="Property Type"
                                        :items="propertyTypes"
                                        item-title="name"
                                        item-value="id"
                                        variant="outlined"
                                        density="compact"
                                        clearable
                                        @update:model-value="applyFilters"
                                    />
                                </v-col>
                            </v-row>

                            <!-- Data Table -->
                            <v-data-table
                                :headers="headers"
                                :items="invoices.data"
                                :items-per-page="invoices.per_page"
                                hide-default-footer
                            >
                                <template #item.invoice_number="{ item }">
                                    <strong>{{ item.invoice_number }}</strong>
                                </template>

                                <template #item.customer="{ item }">
                                    <div>
                                        <div>{{ item.customer.name }}</div>
                                        <small class="text-grey">{{ item.customer.type }}</small>
                                    </div>
                                </template>

                                <template #item.property="{ item }">
                                    <div v-if="item.property">
                                        <div>{{ item.property.number_type }}: {{ item.property.number_value }}</div>
                                        <small class="text-grey">{{ item.property.property_type }}</small>
                                    </div>
                                    <span v-else class="text-grey">N/A</span>
                                </template>

                                <template #item.amount="{ item }">
                                    {{ formatCurrency(item.amount) }}
                                </template>

                                <template #item.paid_amount="{ item }">
                                    {{ formatCurrency(item.paid_amount) }}
                                </template>

                                <template #item.payment_status="{ item }">
                                    <v-chip
                                        :color="getStatusColor(item.payment_status)"
                                        size="small"
                                        variant="flat"
                                    >
                                        {{ item.payment_status }}
                                    </v-chip>
                                </template>

                                <template #item.due_date="{ item }">
                                    <div>
                                        {{ item.due_date }}
                                        <v-chip
                                            v-if="item.is_overdue"
                                            color="error"
                                            size="x-small"
                                            class="ml-1"
                                        >
                                            OVERDUE
                                        </v-chip>
                                    </div>
                                </template>

                                <template #item.giras_synced="{ item }">
                                    <v-icon
                                        :color="item.giras_synced ? 'success' : 'grey'"
                                        size="small"
                                    >
                                        {{ item.giras_synced ? 'mdi-check-circle' : 'mdi-close-circle' }}
                                    </v-icon>
                                </template>

                                <template #item.actions="{ item }">
                                    <v-btn
                                        icon="mdi-eye"
                                        size="small"
                                        variant="text"
                                        @click="$inertia.visit(route('admin.invoices.show', item.id))"
                                    />
                                    <v-btn
                                        v-if="item.payment_status !== 'PAID'"
                                        icon="mdi-pencil"
                                        size="small"
                                        variant="text"
                                        @click="$inertia.visit(route('admin.invoices.edit', item.id))"
                                    />
                                    <v-btn
                                        v-if="item.payment_status !== 'PAID'"
                                        icon="mdi-delete"
                                        size="small"
                                        variant="text"
                                        color="error"
                                        @click="confirmDelete(item)"
                                    />
                                </template>
                            </v-data-table>

                            <!-- Pagination -->
                            <v-pagination
                                v-if="invoices.last_page > 1"
                                v-model="currentPage"
                                :length="invoices.last_page"
                                class="mt-4"
                                @update:model-value="changePage"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Props {
    invoices: any;
    propertyTypes: any[];
    filters: {
        search?: string;
        payment_status?: string;
        property_type?: number;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const paymentStatus = ref(props.filters.payment_status || '');
const propertyType = ref(props.filters.property_type || null);
const currentPage = ref(props.invoices.current_page);

const statusOptions = [
    { title: 'Pending', value: 'PENDING' },
    { title: 'Paid', value: 'PAID' },
    { title: 'Partially Paid', value: 'PARTIALLY_PAID' },
    { title: 'Cancelled', value: 'CANCELLED' },
];

const headers = [
    { title: 'Invoice #', key: 'invoice_number', sortable: false },
    { title: 'Customer', key: 'customer', sortable: false },
    { title: 'Property', key: 'property', sortable: false },
    { title: 'Amount', key: 'amount', sortable: false },
    { title: 'Paid', key: 'paid_amount', sortable: false },
    { title: 'Status', key: 'payment_status', sortable: false },
    { title: 'Due Date', key: 'due_date', sortable: false },
    { title: 'GIRAS', key: 'giras_synced', sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
];

let debounceTimer: ReturnType<typeof setTimeout>;

const debouncedSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    router.get(route('admin.invoices.index'), {
        search: search.value,
        payment_status: paymentStatus.value,
        property_type: propertyType.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const changePage = (page: number) => {
    router.get(route('admin.invoices.index'), {
        page,
        search: search.value,
        payment_status: paymentStatus.value,
        property_type: propertyType.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
    }).format(amount);
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        'PAID': 'success',
        'PENDING': 'warning',
        'PARTIALLY_PAID': 'info',
        'CANCELLED': 'error',
    };
    return colors[status] || 'grey';
};

const confirmDelete = (item: any) => {
    if (confirm(`Are you sure you want to delete invoice ${item.invoice_number}?`)) {
        router.delete(route('admin.invoices.destroy', item.id), {
            preserveScroll: true,
        });
    }
};
</script>

