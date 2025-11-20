<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface Lga {
    id: number
    name: string
}

interface Customer {
    id: number
    first_name: string
    middle_name?: string
    last_name: string
    nin?: string
    gender?: string
    dob?: string
    lga_id?: number
    email?: string
    phone_number?: string
    address?: string
}

interface Props {
    customer: Customer
    lgas: Lga[]
}

const props = defineProps<Props>()

const form = ref({
    first_name: props.customer.first_name,
    middle_name: props.customer.middle_name || '',
    last_name: props.customer.last_name,
    nin: props.customer.nin || '',
    gender: props.customer.gender || '',
    dob: props.customer.dob || '',
    lga_id: props.customer.lga_id || null,
    email: props.customer.email || '',
    phone_number: props.customer.phone_number || '',
    address: props.customer.address || '',
})

const genderOptions = ['Male', 'Female']

const submit = () => {
    router.put(route('admin.individual-customers.update', props.customer.id), form.value)
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>Edit Individual Customer</v-card-title>
                        <v-card-text>
                            <v-form @submit.prevent="submit">
                                <v-row>
                                    <v-col cols="12" md="4">
                                        <v-text-field
                                            v-model="form.first_name"
                                            label="First Name *"
                                            required
                                        />
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-text-field
                                            v-model="form.middle_name"
                                            label="Middle Name"
                                        />
                                    </v-col>
                                    <v-col cols="12" md="4">
                                        <v-text-field
                                            v-model="form.last_name"
                                            label="Last Name *"
                                            required
                                        />
                                    </v-col>

                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.nin"
                                            label="NIN (National Identification Number)"
                                        />
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-select
                                            v-model="form.gender"
                                            :items="genderOptions"
                                            label="Gender"
                                        />
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-text-field
                                            v-model="form.dob"
                                            label="Date of Birth"
                                            type="date"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="6">
                                        <v-select
                                            v-model="form.lga_id"
                                            :items="lgas"
                                            item-title="name"
                                            item-value="id"
                                            label="Local Government Area"
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
                                        @click="router.visit(route('admin.individual-customers.index'))"
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

