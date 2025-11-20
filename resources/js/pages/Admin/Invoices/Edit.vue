<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Edit Invoice</span>
                            <v-btn
                                variant="text"
                                prepend-icon="mdi-arrow-left"
                                @click="$inertia.visit(route('admin.invoices.index'))"
                            >
                                Back to Invoices
                            </v-btn>
                        </v-card-title>

                        <v-card-text>
                            <v-form @submit.prevent="submitForm">
                                <v-row>
                                    <!-- Invoice Number (Read-only) -->
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            :model-value="invoice.invoice_number"
                                            label="Invoice Number"
                                            variant="outlined"
                                            density="comfortable"
                                            readonly
                                            disabled
                                        />
                                    </v-col>

                                    <!-- Property (Read-only) -->
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            :model-value="`${invoice.property.number_type}: ${invoice.property.number_value}`"
                                            label="Property"
                                            variant="outlined"
                                            density="comfortable"
                                            readonly
                                            disabled
                                        />
                                    </v-col>

                                    <!-- Amount -->
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model.number="form.amount"
                                            label="Amount (₦) *"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="form.errors.amount"
                                        />
                                    </v-col>

                                    <!-- Due Date - Dynamic based on payment frequency -->
                                    <v-col cols="12" md="6">
                                        <!-- Daily: Full date picker -->
                                        <v-menu
                                            v-if="invoice.property.payment_frequency === 'daily'"
                                            v-model="dateMenu"
                                            :close-on-content-click="false"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template #activator="{ props: menuProps }">
                                                <v-text-field
                                                    v-model="formattedDueDate"
                                                    label="Due Date (Daily) *"
                                                    prepend-inner-icon="mdi-calendar"
                                                    readonly
                                                    variant="outlined"
                                                    density="comfortable"
                                                    v-bind="menuProps"
                                                    :error-messages="form.errors.due_date"
                                                    hint="Select specific date"
                                                    persistent-hint
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="datePickerValue"
                                                @update:model-value="onDateSelected"
                                            />
                                        </v-menu>

                                        <!-- Weekly: Full date picker -->
                                        <v-menu
                                            v-else-if="invoice.property.payment_frequency === 'weekly'"
                                            v-model="dateMenu"
                                            :close-on-content-click="false"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template #activator="{ props: menuProps }">
                                                <v-text-field
                                                    v-model="formattedDueDate"
                                                    label="Due Date (Weekly) *"
                                                    prepend-inner-icon="mdi-calendar"
                                                    readonly
                                                    variant="outlined"
                                                    density="comfortable"
                                                    v-bind="menuProps"
                                                    :error-messages="form.errors.due_date"
                                                    hint="Select specific date"
                                                    persistent-hint
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="datePickerValue"
                                                @update:model-value="onDateSelected"
                                            />
                                        </v-menu>

                                        <!-- Monthly: Month and Year picker (defaults to 1st of month) -->
                                        <v-menu
                                            v-else-if="invoice.property.payment_frequency === 'monthly'"
                                            v-model="dateMenu"
                                            :close-on-content-click="false"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template #activator="{ props: menuProps }">
                                                <v-text-field
                                                    v-model="formattedDueDate"
                                                    label="Due Date (Monthly - 1st of month) *"
                                                    prepend-inner-icon="mdi-calendar-month"
                                                    readonly
                                                    variant="outlined"
                                                    density="comfortable"
                                                    v-bind="menuProps"
                                                    :error-messages="form.errors.due_date"
                                                    hint="Automatically set to 1st of selected month"
                                                    persistent-hint
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="datePickerValue"
                                                @update:model-value="onMonthSelected"
                                            />
                                        </v-menu>

                                        <!-- Yearly: Year picker (defaults to Jan 1st) -->
                                        <v-menu
                                            v-else-if="invoice.property.payment_frequency === 'yearly'"
                                            v-model="dateMenu"
                                            :close-on-content-click="false"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template #activator="{ props: menuProps }">
                                                <v-text-field
                                                    v-model="formattedDueDate"
                                                    label="Due Date (Yearly - Jan 1st) *"
                                                    prepend-inner-icon="mdi-calendar-blank"
                                                    readonly
                                                    variant="outlined"
                                                    density="comfortable"
                                                    v-bind="menuProps"
                                                    :error-messages="form.errors.due_date"
                                                    hint="Automatically set to January 1st of selected year"
                                                    persistent-hint
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="datePickerValue"
                                                @update:model-value="onYearSelected"
                                            />
                                        </v-menu>

                                        <!-- Default: Regular date input -->
                                        <v-text-field
                                            v-else
                                            v-model="form.due_date"
                                            label="Due Date *"
                                            type="date"
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="form.errors.due_date"
                                        />
                                    </v-col>

                                    <!-- Description -->
                                    <v-col cols="12">
                                        <v-textarea
                                            v-model="form.description"
                                            label="Description"
                                            variant="outlined"
                                            density="comfortable"
                                            rows="3"
                                            :error-messages="form.errors.description"
                                        />
                                    </v-col>

                                    <!-- Property Information (Read-only) -->
                                    <v-col cols="12">
                                        <v-card variant="outlined" color="grey-lighten-4">
                                            <v-card-title class="text-subtitle-1">
                                                Property Information
                                            </v-card-title>
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" md="4">
                                                        <div class="text-caption text-grey">Address</div>
                                                        <div class="text-body-1">{{ invoice.property.address }}</div>
                                                    </v-col>
                                                    <v-col cols="12" md="4">
                                                        <div class="text-caption text-grey">Property Type</div>
                                                        <div class="text-body-1">{{ invoice.property.property_type }}</div>
                                                    </v-col>
                                                    <v-col cols="12" md="4">
                                                        <div class="text-caption text-grey">Default Amount</div>
                                                        <div class="text-body-1">{{ formatCurrency(invoice.property.amount) }}</div>
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Submit Button -->
                                    <v-col cols="12">
                                        <v-btn
                                            type="submit"
                                            color="primary"
                                            size="large"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                        >
                                            Update Invoice
                                        </v-btn>
                                        <v-btn
                                            variant="text"
                                            size="large"
                                            class="ml-2"
                                            @click="$inertia.visit(route('admin.invoices.index'))"
                                        >
                                            Cancel
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Props {
    invoice: any;
    properties: any[];
}

const props = defineProps<Props>();

const form = useForm({
    amount: props.invoice.amount,
    due_date: props.invoice.due_date,
    description: props.invoice.description,
});

const dateMenu = ref(false);
const datePickerValue = ref<Date | null>(props.invoice.due_date ? new Date(props.invoice.due_date) : null);

// Computed property for formatted due date display
const formattedDueDate = computed(() => {
    if (!form.due_date) return '';

    const date = new Date(form.due_date);
    const frequency = props.invoice.property.payment_frequency;

    if (frequency === 'monthly') {
        // Show as "Month Year" for monthly
        return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    } else if (frequency === 'yearly') {
        // Show as "Year" for yearly
        return date.getFullYear().toString();
    } else {
        // Show full date for daily/weekly
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
});

// Handle date selection for daily/weekly (full date)
const onDateSelected = (date: Date | null) => {
    if (date) {
        // Convert Date object to YYYY-MM-DD string
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        form.due_date = `${year}-${month}-${day}`;
        dateMenu.value = false;
    }
};

// Handle month selection for monthly (set to 1st of month)
const onMonthSelected = (date: Date | null) => {
    if (date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        // Always set to 1st of the month
        form.due_date = `${year}-${month}-01`;
        dateMenu.value = false;
    }
};

// Handle year selection for yearly (set to Jan 1st)
const onYearSelected = (date: Date | null) => {
    if (date) {
        const year = date.getFullYear();
        // Always set to January 1st
        form.due_date = `${year}-01-01`;
        dateMenu.value = false;
    }
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
    }).format(amount);
};

const submitForm = () => {
    form.put(route('admin.invoices.update', props.invoice.id), {
        preserveScroll: true,
    });
};
</script>

