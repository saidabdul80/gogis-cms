<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Create Invoice</span>
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
                                    <!-- Property Selection -->
                                    <v-col cols="12" md="6">
                                        <v-autocomplete
                                            v-model="form.property_id"
                                            label="Select Property *"
                                            :items="properties"
                                            :item-title="getPropertyTitle"
                                            item-value="id"
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="form.errors.property_id"
                                            @update:model-value="onPropertySelected"
                                        >
                                            <template #item="{ props: itemProps, item }">
                                                <v-list-item v-bind="itemProps">
                                                    <template #title>
                                                        <strong>{{ item.raw.number_type }}: {{ item.raw.number_value }}</strong>
                                                    </template>
                                                    <template #subtitle>
                                                        <div>{{ item.raw.address }}</div>
                                                        <div class="text-caption">
                                                            {{ item.raw.property_type }} | {{ item.raw.customer.name }}
                                                        </div>
                                                    </template>
                                                </v-list-item>
                                            </template>
                                        </v-autocomplete>
                                    </v-col>

                                    <!-- GIRAS Variables (dynamic fields) -->
                                    <v-col
                                        cols="12"
                                        md="6"
                                        v-for="(value, variableName) in form.giras_variables"
                                        :key="String(variableName)"
                                    >
                                        <v-text-field
                                            v-model.number="form.giras_variables[variableName]"
                                            :label="`${String(variableName).replace(/_/g, ' ').replace(/\b\w/g, (l: string) => l.toUpperCase())} (₦) *`"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="form.errors[`giras_variables.${variableName}`]"
                                        />
                                    </v-col>

                                    <!-- Due Date - Dynamic based on payment frequency -->
                                    <v-col cols="12" md="6">
                                        <!-- Daily: Full date picker -->
                                        <v-menu
                                            v-if="selectedProperty?.payment_frequency === 'daily'"
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
                                            v-else-if="selectedProperty?.payment_frequency === 'weekly'"
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
                                            v-else-if="selectedProperty?.payment_frequency === 'monthly'"
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
                                            v-else-if="selectedProperty?.payment_frequency === 'yearly'"
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

                                        <!-- Default: Regular date input when no property selected -->
                                        <v-text-field
                                            v-else
                                            v-model="form.due_date"
                                            label="Due Date *"
                                            type="date"
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="form.errors.due_date"
                                            hint="Select a property first"
                                            persistent-hint
                                            disabled
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

                                    <!-- GIRAS Integration Options -->
                                    <v-col cols="12">
                                        <v-card variant="outlined" color="blue-lighten-5">
                                            <v-card-title class="text-subtitle-1">
                                                <v-icon icon="mdi-bank" class="mr-2"></v-icon>
                                                GIRAS Payment Gateway Integration
                                            </v-card-title>
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-switch
                                                            v-model="form.sync_with_giras"
                                                            label="Sync with GIRAS Payment Gateway"
                                                            color="primary"
                                                            hint="Enable to generate payment link through GIRAS"
                                                            persistent-hint
                                                            inset
                                                        />
                                                    </v-col>
                                                    <v-col v-if="form.sync_with_giras" cols="12" md="6">
                                                        <v-select
                                                            v-model="form.giras_gateway"
                                                            label="Payment Gateway"
                                                            :items="['paystack', 'remita', 'interswitch']"
                                                            variant="outlined"
                                                            density="comfortable"
                                                            hint="Select payment gateway for GIRAS"
                                                            persistent-hint
                                                        />
                                                    </v-col>
                                                </v-row>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Customer Information (Read-only) -->
                                    <v-col v-if="selectedProperty" cols="12">
                                        <v-card variant="outlined" color="grey-lighten-4">
                                            <v-card-title class="text-subtitle-1">
                                                Customer Information
                                            </v-card-title>
                                            <v-card-text>
                                                <v-row>
                                                    <v-col cols="12" md="4">
                                                        <div class="text-caption text-grey">Customer Name</div>
                                                        <div class="text-body-1">{{ selectedProperty.customer.name }}</div>
                                                    </v-col>
                                                    <v-col cols="12" md="4">
                                                        <div class="text-caption text-grey">Property Type</div>
                                                        <div class="text-body-1">{{ selectedProperty.property_type }}</div>
                                                    </v-col>
                                                    <v-col cols="12" md="4">
                                                        <div class="text-caption text-grey">Payment Frequency</div>
                                                        <div class="text-body-1 text-capitalize">{{ selectedProperty.payment_frequency }}</div>
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
                                            Create Invoice
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
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';

interface Property {
    id: number;
    number_type: string;
    number_value: string;
    address: string;
    amount: number;
    payment_frequency: string;
    property_type: string;
    customer: {
        id: number;
        name: string;
        type: string;
    };
}

interface GirasConfig {
    variables: Record<string, number>;
    template: string;
}

interface Props {
    properties: Property[];
    selectedProperty?: Property;
    girasConfig?: GirasConfig | null;
}

const props = defineProps<Props>();

// Initialize form with GIRAS variables
const initializeGirasVariables = () => {
    if (props.girasConfig?.variables) {
        const variables = { ...props.girasConfig.variables };
        // If property is pre-selected, set first variable to property amount
        if (props.selectedProperty) {
            const firstKey = Object.keys(variables)[0];
            if (firstKey) {
                variables[firstKey] = props.selectedProperty.amount;
            }
        }
        return variables;
    }
    return {};
};

const form = useForm({
    property_id: props.selectedProperty?.id || null,
    amount: props.selectedProperty?.amount || 0,
    due_date: '',
    description: '',
    sync_with_giras: true, // Default to true for GIRAS integration
    giras_gateway: 'paystack',
    giras_variables: initializeGirasVariables(),
});

const selectedProperty = ref<Property | null>(props.selectedProperty || null);
const dateMenu = ref(false);
const datePickerValue = ref<Date | null>(null);

// Calculate amount from GIRAS template
const calculateAmountFromTemplate = () => {
    if (!props.girasConfig?.template || !form.giras_variables) {
        return;
    }

    try {
        // Replace variables in template with actual values
        let formula = props.girasConfig.template;

        for (const [key, value] of Object.entries(form.giras_variables)) {
            formula = formula.replace(new RegExp(`{${key}}`, 'g'), String(value || 0));
        }

        // Evaluate the formula (basic math operations)
        const result = new Function(`return ${formula}`)();
        form.amount = Number(result);
    } catch (error) {
        console.error('Failed to calculate amount from template:', error);
    }
};

// Watch for changes in GIRAS variables to recalculate amount
watch(() => form.giras_variables, () => {
    calculateAmountFromTemplate();
}, { deep: true });

// Computed property for formatted due date display
const formattedDueDate = computed(() => {
    if (!form.due_date) return '';

    const date = new Date(form.due_date);
    const frequency = selectedProperty.value?.payment_frequency;

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

const getPropertyTitle = (property: Property) => {
    return `${property.number_type}: ${property.number_value} - ${property.address}`;
};

const onPropertySelected = (propertyId: number) => {
    const property = props.properties.find(p => p.id === propertyId);
    if (property) {
        selectedProperty.value = property;
        form.amount = property.amount;
        form.description = `Invoice for ${property.number_type}: ${property.number_value}`;

        // Initialize GIRAS variables - set first variable to property amount
        if (props.girasConfig?.variables) {
            const variableKeys = Object.keys(props.girasConfig.variables);
            form.giras_variables = { ...props.girasConfig.variables };

            // Set the first variable to property amount
            if (variableKeys.length > 0) {
                form.giras_variables[variableKeys[0]] = property.amount;
            }

            // Calculate amount from template
            calculateAmountFromTemplate();
        } else {
            // No GIRAS variables, use property amount directly
            form.amount = property.amount;
        }

        // Reset due date when property changes
        form.due_date = '';
        datePickerValue.value = null;
    }
};

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



const submitForm = () => {
    form.post(route('admin.invoices.store'), {
        preserveScroll: true,
    });
};
</script>

