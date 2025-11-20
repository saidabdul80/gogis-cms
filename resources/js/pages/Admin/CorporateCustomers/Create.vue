<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const form = ref({
    name: '',
    category: '',
    registration_number: '',
    registered_date: '',
    email: '',
    phone_number: '',
    address: '',
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
    router.post(route('admin.corporate-customers.store'), form.value)
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>Add Corporate Customer</v-card-title>
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
                                        Save Customer
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

