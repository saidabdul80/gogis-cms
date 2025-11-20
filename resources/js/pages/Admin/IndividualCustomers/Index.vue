<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface Customer {
    id: number
    first_name: string
    middle_name?: string
    last_name: string
    nin?: string
    gender?: string
    dob?: string
    email?: string
    phone_number?: string
    address?: string
    lga?: {
        id: number
        name: string
    }
    created_at: string
}

interface Lga {
    id: number
    name: string
    code?: string
}

interface Props {
    customers: {
        data: Customer[]
        current_page: number
        last_page: number
        per_page: number
        total: number
    }
    lgas: Lga[]
    filters: {
        search?: string
        gender?: string
        lga_id?: number
    }
}

const props = defineProps<Props>()

const deleteDialog = ref(false)
const customerToDelete = ref<Customer | null>(null)

const search = ref(props.filters.search || '')
const genderFilter = ref(props.filters.gender || '')
const lgaFilter = ref(props.filters.lga_id || '')

const genderOptions = [
    { title: 'All', value: '' },
    { title: 'Male', value: 'Male' },
    { title: 'Female', value: 'Female' }
]

const lgaOptions = [
    { title: 'All LGAs', value: '' },
    ...props.lgas.map(lga => ({ title: lga.name, value: lga.id }))
]

watch([search, genderFilter, lgaFilter], () => {
    router.get(route('admin.individual-customers.index'), {
        search: search.value,
        gender: genderFilter.value,
        lga_id: lgaFilter.value
    }, {
        preserveState: true,
        replace: true
    })
}, { debounce: 300 })

const openDeleteDialog = (customer: Customer) => {
    customerToDelete.value = customer
    deleteDialog.value = true
}

const confirmDelete = () => {
    if (customerToDelete.value) {
        router.delete(route('admin.individual-customers.destroy', customerToDelete.value.id), {
            onSuccess: () => {
                deleteDialog.value = false
                customerToDelete.value = null
            }
        })
    }
}

const getFullName = (customer: Customer) => {
    return [customer.first_name, customer.middle_name, customer.last_name]
        .filter(Boolean)
        .join(' ')
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Individual Customers</span>
                            <Link :href="route('admin.individual-customers.create')">
                                <v-btn color="primary" prepend-icon="mdi-plus">
                                    Add Customer
                                </v-btn>
                            </Link>
                        </v-card-title>

                        <v-card-text>
                            <!-- Filters -->
                            <v-row class="mb-4">
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="search"
                                        label="Search"
                                        prepend-inner-icon="mdi-magnify"
                                        placeholder="Search by name, NIN, email, or phone"
                                        clearable
                                    />
                                </v-col>
                                <v-col cols="12" md="3">
                                    <v-select
                                        v-model="genderFilter"
                                        :items="genderOptions"
                                        label="Gender"
                                    />
                                </v-col>
                                <v-col cols="12" md="3">
                                    <v-select
                                        v-model="lgaFilter"
                                        :items="lgaOptions"
                                        label="LGA"
                                    />
                                </v-col>
                            </v-row>

                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>NIN</th>
                                        <th>Gender</th>
                                        <th>LGA</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="customer in customers.data" :key="customer.id">
                                        <td>{{ getFullName(customer) }}</td>
                                        <td>{{ customer.nin || 'N/A' }}</td>
                                        <td>{{ customer.gender || 'N/A' }}</td>
                                        <td>{{ customer.lga?.name || 'N/A' }}</td>
                                        <td>{{ customer.phone_number || 'N/A' }}</td>
                                        <td>{{ customer.email || 'N/A' }}</td>
                                        <td>
                                            <v-btn
                                                size="small"
                                                color="info"
                                                variant="text"
                                                icon="mdi-eye"
                                                :href="route('admin.individual-customers.show', customer.id)"
                                                @click.prevent="router.visit(route('admin.individual-customers.show', customer.id))"
                                            />
                                            <v-btn
                                                size="small"
                                                color="primary"
                                                variant="text"
                                                icon="mdi-pencil"
                                                :href="route('admin.individual-customers.edit', customer.id)"
                                                @click.prevent="router.visit(route('admin.individual-customers.edit', customer.id))"
                                            />
                                            <v-btn
                                                size="small"
                                                color="error"
                                                variant="text"
                                                icon="mdi-delete"
                                                @click="openDeleteDialog(customer)"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>

                            <v-pagination
                                v-if="customers.last_page > 1"
                                :length="customers.last_page"
                                :model-value="customers.current_page"
                                @update:model-value="(page) => router.visit(route('admin.individual-customers.index', { page, search: search, gender: genderFilter, lga_id: lgaFilter }))"
                                class="mt-4"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title>Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete {{ customerToDelete ? getFullName(customerToDelete) : '' }}?
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn color="error" @click="confirmDelete">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AdminLayout>
</template>

