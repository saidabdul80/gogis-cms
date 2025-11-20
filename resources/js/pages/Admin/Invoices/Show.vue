<template>
    <AdminLayout>
        <v-container fluid>
            <!-- Action Buttons -->
            <div class="d-flex justify-end mb-4 no-print">
                <v-btn
                    v-if="invoice.payment_status === 'PAID'"
                    color="primary"
                    prepend-icon="mdi-printer"
                    class="mr-2"
                    @click="printReceipt"
                >
                    Print Receipt
                </v-btn>
                <v-btn
                    v-if="invoice.payment_status !== 'PAID' && invoice.giras.synced"
                    color="success"
                    prepend-icon="mdi-credit-card"
                    class="mr-2"
                    @click="initiatePayment"
                    :loading="paymentLoading"
                >
                    Pay Now
                </v-btn>
                <v-btn
                    v-if="invoice.payment_status !== 'PAID'"
                    color="primary"
                    prepend-icon="mdi-pencil"
                    class="mr-2"
                    @click="router.visit(route('admin.invoices.edit', invoice.id))"
                >
                    Edit
                </v-btn>
                <v-btn
                    variant="text"
                    prepend-icon="mdi-arrow-left"
                    @click="router.visit(route('admin.invoices.index'))"
                >
                    Back
                </v-btn>
            </div>

            <!-- Payment Dialog -->
            <v-dialog v-model="paymentDialog" max-width="900" persistent class="no-print">
                <v-card>
                    <v-card-title class="d-flex justify-space-between align-center bg-success">
                        <span class="text-white">Payment Gateway</span>
                        <v-btn icon="mdi-close" variant="text" color="white" @click="closePaymentDialog"></v-btn>
                    </v-card-title>

                    <v-card-text class="pa-0">
                        <!-- If payment link is a URL, show iframe -->
                        <div v-if="isPaymentUrl" style="height: 600px;">
                            <iframe
                                :src="paymentLink"
                                style="width: 100%; height: 100%; border: none;"
                                title="Payment Gateway"
                            ></iframe>
                        </div>

                        <!-- If it's just a reference (RRR), show details -->
                        <div v-else class="pa-8 text-center">
                            <v-icon size="64" color="success" class="mb-4">mdi-check-circle</v-icon>
                            <h2 class="text-h5 mb-4">Payment Reference Generated</h2>

                            <v-card variant="outlined" class="mb-4">
                                <v-card-text>
                                    <div class="mb-4">
                                        <div class="text-caption text-grey">Gateway</div>
                                        <div class="text-h6 text-uppercase">{{ paymentGateway }}</div>
                                    </div>

                                    <v-divider class="my-4"></v-divider>

                                    <div>
                                        <div class="text-caption text-grey">{{ paymentGateway === 'remita' ? 'RRR Number' : 'Reference' }}</div>
                                        <div class="text-h5 font-weight-bold text-success">{{ paymentReference }}</div>
                                    </div>
                                </v-card-text>
                            </v-card>

                            <v-alert type="info" variant="tonal" class="text-left">
                                <template v-if="paymentGateway === 'remita'">
                                    <strong>How to Pay:</strong>
                                    <ol class="mt-2">
                                        <li>Visit any bank or use your bank's mobile app</li>
                                        <li>Select "Remita Payment"</li>
                                        <li>Enter the RRR number above</li>
                                        <li>Complete the payment</li>
                                    </ol>
                                </template>
                                <template v-else>
                                    Use the reference number above to complete your payment through {{ paymentGateway }}.
                                </template>
                            </v-alert>

                            <v-btn
                                color="primary"
                                class="mt-4"
                                @click="copyReference"
                                prepend-icon="mdi-content-copy"
                            >
                                Copy Reference
                            </v-btn>
                        </div>
                    </v-card-text>
                </v-card>
            </v-dialog>

            <!-- Invoice Card -->
            <v-card class="invoice-card" id="invoice-print-area">
                <v-card-title class="bg-primary text-white pa-6">
                    <div class="d-flex justify-space-between align-center flex-wrap">
                        <div>
                            <h2 class="text-h4 font-weight-bold text-white">INVOICE</h2>
                            <div class="text-h6 mt-2 text-white">{{ invoice.invoice_number }}</div>
                        </div>
                        <div class="text-right">
                            <GChip
                                :color="getStatusColor(invoice.payment_status)"
                                size="large"
                                variant="flat"
                                class="font-weight-bold text-white"
                            >
                                {{ invoice.payment_status }}
                            </GChip>
                            <g-chip
                                v-if="invoice.is_overdue"
                                color="error"
                                size="large"
                                class="ml-2 font-weight-bold"
                            >
                                OVERDUE
                            </g-chip>
                        </div>
                    </div>
                </v-card-title>

                <v-card-text class="pa-6">
                    <!-- Main Invoice Details -->
                    <v-row class="mb-6">
                        <!-- Left Column: Customer & Property -->
                        <v-col cols="12" md="6">
                            <div class="mb-6">
                                <h3 class="text-subtitle-1 font-weight-bold text-grey-darken-2 mb-3">BILL TO:</h3>
                                <div class="text-h6 font-weight-bold mb-2">{{ invoice.customer.name }}</div>
                                <div class="text-body-2 text-grey-darken-1">
                                    <div class="mb-1"><v-icon size="small" class="mr-1">mdi-email</v-icon>{{ invoice.customer.email || 'N/A' }}</div>
                                    <div class="mb-1"><v-icon size="small" class="mr-1">mdi-phone</v-icon>{{ invoice.customer.phone || 'N/A' }}</div>
                                    <div><v-chip size="x-small" class="mt-1">{{ invoice.customer.type }}</v-chip></div>
                                </div>
                            </div>

                            <div v-if="invoice.property">
                                <h3 class="text-subtitle-1 font-weight-bold text-grey-darken-2 mb-3">PROPERTY:</h3>
                                <div class="text-body-1 font-weight-bold mb-1">
                                    {{ invoice.property.number_type }}: {{ invoice.property.number_value }}
                                </div>
                                <div class="text-body-2 text-grey-darken-1">
                                    <div class="mb-1"><v-icon size="small" class="mr-1">mdi-map-marker</v-icon>{{ invoice.property.address }}</div>
                                    <div v-if="invoice.property.property_type">
                                        <v-icon size="small" class="mr-1">mdi-home-variant</v-icon>{{ invoice.property.property_type }}
                                    </div>
                                </div>
                            </div>
                        </v-col>

                        <!-- Right Column: Invoice Details -->
                        <v-col cols="12" md="6">
                            <v-table density="compact" class="invoice-details-table">
                                <tbody>
                                    <tr>
                                        <td class="text-grey-darken-1 font-weight-medium">Issue Date:</td>
                                        <td class="text-right font-weight-bold">{{ invoice.issue_date }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-grey-darken-1 font-weight-medium">Due Date:</td>
                                        <td class="text-right font-weight-bold">{{ invoice.due_date || 'N/A' }}</td>
                                    </tr>
                                    <tr v-if="invoice.description">
                                        <td class="text-grey-darken-1 font-weight-medium">Description:</td>
                                        <td class="text-right">{{ invoice.description }}</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>

                    <v-divider class="my-6"></v-divider>

                    <!-- Amount Summary -->
                    <v-row class="mb-6">
                        <v-col cols="12">
                            <v-table class="amount-summary-table">
                                <tbody>
                                    <tr>
                                        <td class="text-h6 font-weight-medium">Invoice Amount:</td>
                                        <td class="text-right text-h5 font-weight-bold text-primary">{{ formatCurrency(invoice.amount) }}</td>
                                    </tr>
                                    <tr v-if="invoice.paid_amount > 0">
                                        <td class="text-body-1 font-weight-medium text-success">Paid Amount:</td>
                                        <td class="text-right text-h6 font-weight-bold text-success">{{ formatCurrency(invoice.paid_amount) }}</td>
                                    </tr>
                                    <tr v-if="invoice.remaining_amount > 0">
                                        <td class="text-body-1 font-weight-medium text-warning">Remaining Amount:</td>
                                        <td class="text-right text-h6 font-weight-bold text-warning">{{ formatCurrency(invoice.remaining_amount) }}</td>
                                    </tr>
                                </tbody>
                            </v-table>
                        </v-col>
                    </v-row>

                    <!-- GIRAS Integration (Compact) -->
                    <div v-if="invoice.giras.synced" class="mb-6 no-print">
                        <v-divider class="mb-4"></v-divider>
                        <v-expansion-panels variant="accordion">
                            <v-expansion-panel>
                                <v-expansion-panel-title>
                                    <div class="d-flex align-center">
                                        <v-icon class="mr-2" color="success">mdi-cloud-check</v-icon>
                                        <span class="font-weight-medium">GIRAS Integration Details</span>
                                    </div>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text>
                                    <v-row dense>
                                        <v-col cols="6" sm="4" v-if="invoice.giras.invoice_id">
                                            <div class="text-caption text-grey">GIRAS Invoice ID</div>
                                            <div class="text-body-2 font-weight-medium">{{ invoice.giras.invoice_id }}</div>
                                        </v-col>
                                        <v-col cols="6" sm="4" v-if="invoice.giras.invoice_number">
                                            <div class="text-caption text-grey">GIRAS Invoice Number</div>
                                            <div class="text-body-2 font-weight-medium">{{ invoice.giras.invoice_number }}</div>
                                        </v-col>
                                        <v-col cols="6" sm="4" v-if="invoice.giras.gateway">
                                            <div class="text-caption text-grey">Gateway</div>
                                            <v-chip size="x-small" color="info" class="text-uppercase">{{ invoice.giras.gateway }}</v-chip>
                                        </v-col>
                                        <v-col cols="6" sm="4" v-if="invoice.giras.reference">
                                            <div class="text-caption text-grey">Reference</div>
                                            <div class="text-body-2 font-weight-medium">{{ invoice.giras.reference }}</div>
                                        </v-col>
                                        <v-col cols="6" sm="4" v-if="invoice.giras.synced_at">
                                            <div class="text-caption text-grey">Synced At</div>
                                            <div class="text-body-2">{{ invoice.giras.synced_at }}</div>
                                        </v-col>
                                    </v-row>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </div>

                    <!-- Payment History -->
                    <div v-if="invoice.payments.length > 0" class="no-print">
                        <v-divider class="mb-4"></v-divider>
                        <h3 class="text-subtitle-1 font-weight-bold text-grey-darken-2 mb-3">PAYMENT HISTORY</h3>
                        <v-data-table
                            :headers="paymentHeaders"
                            :items="invoice.payments"
                            :items-per-page="-1"
                            hide-default-footer
                            density="compact"
                        >
                            <template #item.amount="{ item }">
                                {{ formatCurrency((item as any).amount) }}
                            </template>
                            <template #item.status="{ item }">
                                <v-chip
                                    :color="(item as any).status === 'SUCCESS' ? 'success' : (item as any).status === 'PENDING' ? 'warning' : 'error'"
                                    size="x-small"
                                    variant="flat"
                                >
                                    {{ (item as any).status }}
                                </v-chip>
                            </template>
                            <template #item.actions="{ item }">
                                <v-btn
                                    v-if="(item as any).status === 'PENDING'"
                                    color="primary"
                                    size="x-small"
                                    variant="text"
                                    prepend-icon="mdi-refresh"
                                    @click="revalidatePayment((item as any).id)"
                                    :loading="revalidatingPaymentId === (item as any).id"
                                >
                                    Revalidate
                                </v-btn>
                            </template>
                        </v-data-table>
                    </div>
                </v-card-text>
            </v-card>
        </v-container>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import GChip from '@/components/GChip.vue';

interface Props {
    invoice: any;
}

const props = defineProps<Props>();
const page = usePage();

const paymentLoading = ref(false);
const paymentDialog = ref(false);
const paymentLink = ref('');
const paymentReference = ref('');
const paymentGateway = ref('');

// Check if payment link is a valid URL
const isPaymentUrl = computed(() => {
    try {
        new URL(paymentLink.value);
        return true;
    } catch {
        return false;
    }
});

// Watch for payment data in flash messages
watch(() => (page.props.flash as any)?.paymentData, (paymentData: any) => {
    if (paymentData) {
        paymentLink.value = paymentData.link || paymentData.reference || '';
        paymentReference.value = paymentData.reference || '';
        paymentGateway.value = paymentData.gateway || '';
        paymentDialog.value = true;
    }
}, { immediate: true });

const initiatePayment = () => {
    paymentLoading.value = true;

    router.post(
        route('admin.invoices.initiate-payment', props.invoice.id),
        {},
        {
            onFinish: () => {
                paymentLoading.value = false;
            },
        }
    );
};

const closePaymentDialog = () => {
    paymentDialog.value = false;
    router.reload();
};

const copyReference = () => {
    navigator.clipboard.writeText(paymentReference.value);
    alert('Reference copied to clipboard!');
};

const printReceipt = () => {
    window.print();
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'PAID':
            return 'success';
        case 'PENDING':
            return 'warning';
        case 'OVERDUE':
            return 'error';
        default:
            return 'grey';
    }
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN'
    }).format(amount);
};

const paymentHeaders = [
    { title: 'Date', key: 'payment_date' },
    { title: 'Reference', key: 'reference' },
    { title: 'Amount', key: 'amount' },
    { title: 'Gateway', key: 'gateway' },
    { title: 'Status', key: 'status' },
    { title: 'Actions', key: 'actions', sortable: false },
];

const revalidatingPaymentId = ref<number | null>(null);

const revalidatePayment = (paymentId: number) => {
    revalidatingPaymentId.value = paymentId;

    router.post(
        route('admin.invoices.payments.revalidate', {
            invoice: props.invoice.id,
            payment: paymentId,
        }),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                revalidatingPaymentId.value = null;
            },
        }
    );
};
</script>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }

    .invoice-card {
        box-shadow: none !important;
        border: 1px solid #ddd;
    }
}

.invoice-details-table tbody tr td {
    border-bottom: none !important;
    padding: 8px 0;
}

.amount-summary-table tbody tr td {
    border-bottom: 1px solid #e0e0e0 !important;
    padding: 12px 0;
}

.amount-summary-table tbody tr:last-child td {
    border-bottom: none !important;
    padding-top: 16px;
}
</style>

