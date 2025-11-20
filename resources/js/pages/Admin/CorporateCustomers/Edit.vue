<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface Customer {
    id: number
    name: string
    category?: string
    registration_number: string
    registered_date?: string
    email?: string
    phone_number?: string
    address?: string
}

interface Props {
    customer: Customer
}

const props = defineProps<Props>()

const form = ref({
    name: props.customer.name,
    category: props.customer.category || '',
    registration_number: props.customer.registration_number,
    registered_date: props.customer.registered_date || '',
    email: props.customer.email || '',
    phone_number: props.customer.phone_number || '',
    address: props.customer.address || '',
})

const categoryOptions = [
    'Company',
    'Hotel',
    'Bank',
    'Hospital',
    'School',
    'MDAs',
    'Others'
]

const submit = () => {
    router.put(route('admin.corporate-customers.update', props.customer.id), form.value)
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>Edit Corporate Customer</v-card-title>
                        <v-card-text>
                            <v-form @submit.prevent="submit">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.name"
                                            label="Company/Organization Name *"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.category"
                                            :items="categoryOptions"
                                            label="Category"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.registration_number"
                                            label="Registration Number *"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.registered_date"
                                            label="Registration Date"
                                            type="date"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.phone_number"
                                            label="Phone Number *"
                                            :error-messages="form.errors.phone_number"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.email"
                                            label="Email"
                                            type="email"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.phone_number"
                                            label="Phone Number *"
                                            type="tel"
                                            placeholder="e.g., 08012345678 or +2348012345678"
                                            hint="Required for GIRAS invoice creation"
                                            persistent-hint
                                            :rules="[
                                                (v: string) => !!v || 'Phone number is required',
                                                (v: string) => (v && v.replace(/[^\d+]/g, '').length >= 11) || 'Phone number must be at least 11 digits'
                                            ]"
                                        />
                                    </v-col>

                                    <v-col cols="12">
                                        <v-textarea
                                            v-model="form.address"
                                            label="Address"
                                            rows="3"
                                        />
                                    </v-col>
                                </v-row>

                                <v-card-actions>
                                    <v-btn
                                        color="grey"
                                        @click="router.visit(route('admin.corporate-customers.index'))"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        color="primary"
                                        type="submit"
                                    >
                                        Update Customer
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

