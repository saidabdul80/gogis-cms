<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import CustomerSelector from '@/components/CustomerSelector.vue'

interface Customer {
    id: number
    name: string
    type: 'individual' | 'corporate'
    email?: string
    phone_number?: string
    customer_id: number
    customer_type: string
}

interface PropertyType {
    id: number
    name: string
    description: string | null
}

interface Property {
    id: number
    property_type_id?: number
    address: string
    // longitude?: number
    // latitude?: number
    // description?: string
    number_type: 'GSL' | 'GM_Number'
    number_value: string
    start_date?: string
    payment_frequency?: string
    amount?: number
    customer_id?: number
    customer_type?: string
    customer?: any
    property_type?: PropertyType
}

interface Props {
    property: Property
    propertyTypes: PropertyType[]
}

const props = defineProps<Props>()

const form = ref({
    property_type_id: props.property.property_type_id || null,
    address: props.property.address,
    // longitude: props.property.longitude || '',
    // latitude: props.property.latitude || '',
    // description: props.property.description || '',
    number_type: props.property.number_type,
    number_value: props.property.number_value,
    start_date: props.property.start_date || '',
    payment_frequency: props.property.payment_frequency || '',
    amount: props.property.amount || '',
    customer_id: props.property.customer_id || null,
    customer_type: props.property.customer_type || '',
})

// Initialize selected customer from property data
const selectedCustomer = ref<Customer | null>(
    props.property.customer ? {
        id: props.property.customer.id,
        name: props.property.customer.name || `${props.property.customer.first_name} ${props.property.customer.last_name}`,
        type: props.property.customer_type?.includes('Individual') ? 'individual' : 'corporate',
        email: props.property.customer.email,
        phone_number: props.property.customer.phone_number,
        customer_id: props.property.customer.id,
        customer_type: props.property.customer_type || ''
    } : null
)

// Watch for customer selection changes
const handleCustomerChange = (customer: Customer | null) => {
    if (customer) {
        form.value.customer_id = customer.customer_id
        form.value.customer_type = customer.customer_type
    } else {
        form.value.customer_id = null
        form.value.customer_type = ''
    }
}

const numberTypeOptions = ['GSL', 'GM_Number']
const paymentFrequencyOptions = [
    { title: 'Daily', value: 'daily' },
    { title: 'Weekly', value: 'weekly' },
    { title: 'Monthly', value: 'monthly' },
    { title: 'Yearly', value: 'yearly' },
]

const submit = () => {
    router.put(route('admin.properties.update', props.property.id), form.value)
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>Edit Property</v-card-title>
                        <v-card-text>
                            <v-form @submit.prevent="submit">
                                <v-row>
                                    <!-- Customer Selection -->
                                    <v-col cols="12" md="6">
                                        <CustomerSelector
                                            v-model="selectedCustomer"
                                            label="Select Customer *"
                                            :required="true"
                                            @update:model-value="handleCustomerChange"
                                        />
                                    </v-col>

                                    <!-- Property Type -->
                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.property_type_id"
                                            :items="propertyTypes"
                                            item-title="name"
                                            item-value="id"
                                            label="Property Type *"
                                            variant="outlined"
                                            required
                                        />
                                    </v-col>

                                    <!-- Property Number -->
                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.number_type"
                                            :items="numberTypeOptions"
                                            label="Number Type *"
                                            variant="outlined"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.number_value"
                                            label="Number Value *"
                                            variant="outlined"
                                            required
                                            hint="e.g., GSL/2024/001 or GM/2024/001"
                                        />
                                    </v-col>

                                    <!-- Address -->
                                    <v-col cols="12">
                                        <v-textarea
                                            v-model="form.address"
                                            label="Address *"
                                            variant="outlined"
                                            required
                                            rows="2"
                                        />
                                    </v-col>

                                    <!-- Coordinates -->
                                    <!-- <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.longitude"
                                            label="Longitude"
                                            variant="outlined"
                                            type="number"
                                            step="0.0000001"
                                            min="-180"
                                            max="180"
                                            hint="e.g., 11.1667"
                                        />
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.latitude"
                                            label="Latitude"
                                            variant="outlined"
                                            type="number"
                                            step="0.0000001"
                                            min="-90"
                                            max="90"
                                            hint="e.g., 10.2833"
                                        />
                                    </v-col> -->

                                    <!-- Start Date -->
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.start_date"
                                            label="Start Date"
                                            variant="outlined"
                                            type="date"
                                        />
                                    </v-col>

                                    <!-- Payment Frequency -->
                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.payment_frequency"
                                            :items="paymentFrequencyOptions"
                                            label="Payment Frequency"
                                            variant="outlined"
                                        />
                                    </v-col>

                                    <!-- Amount -->
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.amount"
                                            label="Amount (₦)"
                                            variant="outlined"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            prefix="₦"
                                        />
                                    </v-col>

                                    <!-- Description -->
                                    <!-- <v-col cols="12" md="6">
                                        <v-textarea
                                            v-model="form.description"
                                            label="Description"
                                            variant="outlined"
                                            rows="3"
                                        />
                                    </v-col> -->
                                </v-row>

                                <v-card-actions>
                                    <v-btn
                                        color="grey"
                                        @click="router.visit(route('admin.properties.index'))"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        color="primary"
                                        type="submit"
                                    >
                                        Update Property
                                    </v-btn>
                                </v-card-actions>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>
