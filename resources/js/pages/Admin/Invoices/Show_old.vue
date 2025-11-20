<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
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
                            @click="$inertia.visit(route('admin.invoices.edit', invoice.id))"
                        >
                            Edit
                        </v-btn>
                        <v-btn
                            variant="text"
                            prepend-icon="mdi-arrow-left"
                            @click="$inertia.visit(route('admin.invoices.index'))"
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
                    <v-card class="invoice-card">
                        <v-card-title class="bg-primary text-white pa-6">
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <h2 class="text-h4 font-weight-bold">INVOICE</h2>
                                    <div class="text-h6 mt-2">{{ invoice.invoice_number }}</div>
                                </div>
                                <div class="text-right">
                                    <v-chip
                                        :color="getStatusColor(invoice.payment_status)"
                                        size="large"
                                        variant="flat"
                                        class="font-weight-bold"
                                    >
                                        {{ invoice.payment_status }}
                                    </v-chip>
                                    <v-chip
                                        v-if="invoice.is_overdue"
                                        color="error"
                                        size="large"
                                        class="ml-2 font-weight-bold"
                                    >
                                        OVERDUE
                                    </v-chip>
                                </div>
                            </div>
                        </v-card-title>

                        <v-card-text>
                            <!-- Invoice Information Section -->
                            <div class="mb-6">
                                <h3 class="text-h6 mb-3 d-flex align-center">
                                    <v-icon class="mr-2" color="primary">mdi-file-document</v-icon>
                                    Invoice Information 
                                </h3>
                                <hr class="mb-1"></hr>
                                <v-row dense>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Invoice Number</div>
                                        <div class="text-body-1 font-weight-bold">{{ invoice.invoice_number }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Status</div>
                                        <div>
                                            <v-chip
                                                :color="getStatusColor(invoice.payment_status)"
                                                size="small"
                                                variant="flat"
                                            >
                                                {{ invoice.payment_status }}
                                            </v-chip>
                                            <v-chip
                                                v-if="invoice.is_overdue"
                                                color="error"
                                                size="small"
                                                class="ml-1"
                                            >
                                                OVERDUE
                                            </v-chip>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Issue Date</div>
                                        <div class="text-body-1">{{ invoice.issue_date }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Due Date</div>
                                        <div class="text-body-1">{{ invoice.due_date || 'N/A' }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Amount</div>
                                        <div class="text-h6 text-primary font-weight-bold">{{ formatCurrency(invoice.amount) }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Paid Amount</div>
                                        <div class="text-h6 text-success font-weight-bold">{{ formatCurrency(invoice.paid_amount) }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Remaining Amount</div>
                                        <div class="text-h6 text-warning font-weight-bold">{{ formatCurrency(invoice.remaining_amount) }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-caption text-grey mb-1">Description</div>
                                        <div class="text-body-1">{{ invoice.description || 'N/A' }}</div>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Customer Information Section -->
                            <div class="mb-6">
                                <h3 class="text-h6 mb-3 d-flex align-center">
                                    <v-icon class="mr-2" color="primary">mdi-account</v-icon>
                                    Customer Information
                                </h3>
                                <hr class="mb-1"></hr>

                                <v-row dense>
                                    <v-col cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Name</div>
                                        <div class="text-body-1 font-weight-bold">{{ invoice.customer.name }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="2">
                                        <div class="text-caption text-grey mb-1">Type</div>
                                        <v-chip size="small" color="primary" variant="tonal">{{ invoice.customer.type }}</v-chip>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Email</div>
                                        <div class="text-body-1">{{ invoice.customer.email || 'N/A' }}</div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Phone</div>
                                        <div class="text-body-1">{{ invoice.customer.phone || 'N/A' }}</div>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Property Information Section -->
                            <div v-if="invoice.property" class="mb-6">
                                <h3 class="text-h6 mb-3 d-flex align-center">
                                    <v-icon class="mr-2" color="primary">mdi-home</v-icon>
                                    Property Information
                                </h3>
                                <v-divider class="mb-4"></v-divider>
                                <v-row dense>
                                    <v-col cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Property Number</div>
                                        <div class="text-body-1 font-weight-bold">
                                            {{ invoice.property.number_type }}: {{ invoice.property.number_value }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Property Type</div>
                                        <div class="text-body-1">{{ invoice.property.property_type || 'N/A' }}</div>
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <div class="text-caption text-grey mb-1">Address</div>
                                        <div class="text-body-1">{{ invoice.property.address }}</div>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- GIRAS Integration Section -->
                            <div class="mb-6">
                                <h3 class="text-h6 mb-3 d-flex align-center">
                                    <v-icon class="mr-2" color="primary">mdi-cloud-sync</v-icon>
                                    GIRAS Integration
                                </h3>
                                <v-divider class="mb-4"></v-divider>
                                <v-row dense>
                                    <v-col cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Sync Status</div>
                                        <v-chip
                                            :color="invoice.giras.synced ? 'success' : 'grey'"
                                            size="small"
                                            variant="flat"
                                        >
                                            <v-icon start size="small">{{ invoice.giras.synced ? 'mdi-check-circle' : 'mdi-close-circle' }}</v-icon>
                                            {{ invoice.giras.synced ? 'Synced' : 'Not Synced' }}
                                        </v-chip>
                                    </v-col>
                                    <v-col v-if="invoice.giras.invoice_id" cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">GIRAS Invoice ID</div>
                                        <div class="text-body-1 font-weight-medium">{{ invoice.giras.invoice_id }}</div>
                                    </v-col>
                                    <v-col v-if="invoice.giras.invoice_number" cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">GIRAS Invoice Number</div>
                                        <div class="text-body-1 font-weight-medium">{{ invoice.giras.invoice_number }}</div>
                                    </v-col>
                                    <v-col v-if="invoice.giras.gateway" cols="12" sm="6" md="3">
                                        <div class="text-caption text-grey mb-1">Gateway</div>
                                        <v-chip size="small" color="info" variant="tonal" class="text-uppercase">
                                            {{ invoice.giras.gateway }}
                                        </v-chip>
                                    </v-col>
                                    <v-col v-if="invoice.giras.reference" cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Payment Reference</div>
                                        <div class="text-body-1 font-weight-medium">{{ invoice.giras.reference }}</div>
                                    </v-col>
                                    <v-col v-if="invoice.giras.synced_at" cols="12" sm="6" md="4">
                                        <div class="text-caption text-grey mb-1">Synced At</div>
                                        <div class="text-body-1">{{ invoice.giras.synced_at }}</div>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Payment History Section -->
                            <div v-if="invoice.payments.length > 0">
                                <h3 class="text-h6 mb-3 d-flex align-center">
                                    <v-icon class="mr-2" color="primary">mdi-history</v-icon>
                                    Payment History
                                </h3>
                                <v-divider class="mb-4"></v-divider>
                                <v-data-table
                                    :headers="paymentHeaders"
                                    :items="invoice.payments"
                                    :items-per-page="-1"
                                    hide-default-footer
                                    class="elevation-0"
                                >
                                    <template #item.amount="{ item }">
                                        {{ formatCurrency(item.amount) }}
                                    </template>
                                    <template #item.status="{ item }">
                                        <v-chip
                                            :color="item.status === 'SUCCESS' ? 'success' : item.status === 'PENDING' ? 'warning' : 'error'"
                                            size="small"
                                            variant="flat"
                                        >
                                            {{ item.status }}
                                        </v-chip>
                                    </template>
                                </v-data-table>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ref, computed, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

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
    // Reload the page to refresh invoice data
    router.reload();
};

const copyReference = () => {
    navigator.clipboard.writeText(paymentReference.value);
    // You can add a toast notification here if you have one
    alert('Reference copied to clipboard!');
};

const paymentHeaders = [
    { title: 'Reference', key: 'reference' },
    { title: 'Amount', key: 'amount' },
    { title: 'Status', key: 'status' },
    { title: 'Date', key: 'payment_date' },
    { title: 'Gateway', key: 'gateway' },
];

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
</script>

