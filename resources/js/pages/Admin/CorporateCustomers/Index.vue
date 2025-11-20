<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
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
    created_at: string
}

interface Props {
    customers: {
        data: Customer[]
        current_page: number
        last_page: number
        per_page: number
        total: number
    }
    filters: {
        search?: string
        category?: string
    }
}

const props = defineProps<Props>()

const deleteDialog = ref(false)
const customerToDelete = ref<Customer | null>(null)

const search = ref(props.filters.search || '')
const categoryFilter = ref(props.filters.category || '')

const categoryOptions = [
    { title: 'All', value: '' },
    { title: 'Company', value: 'Company' },
    { title: 'Hotel', value: 'Hotel' },
    { title: 'Bank', value: 'Bank' },
    { title: 'Hospital', value: 'Hospital' },
    { title: 'School', value: 'School' },
    { title: 'MDAs', value: 'MDAs' },
    { title: 'Others', value: 'Others' }
]

watch([search, categoryFilter], () => {
    router.get(route('admin.corporate-customers.index'), {
        search: search.value,
        category: categoryFilter.value
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
        router.delete(route('admin.corporate-customers.destroy', customerToDelete.value.id), {
            onSuccess: () => {
                deleteDialog.value = false
                customerToDelete.value = null
            }
        })
    }
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Corporate Customers</span>
                            <Link :href="route('admin.corporate-customers.create')">
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
                                        placeholder="Search by name, registration number, email, or phone"
                                        clearable
                                    />
                                </v-col>
                                <v-col cols="12" md="3">
                                    <v-select
                                        v-model="categoryFilter"
                                        :items="categoryOptions"
                                        label="Category"
                                    />
                                </v-col>
                            </v-row>

                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Registration Number</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="customer in customers.data" :key="customer.id">
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.category || 'N/A' }}</td>
                                        <td>{{ customer.registration_number }}</td>
                                        <td>{{ customer.phone_number || 'N/A' }}</td>
                                        <td>{{ customer.email || 'N/A' }}</td>
                                        <td>
                                            <v-btn
                                                size="small"
                                                color="info"
                                                variant="text"
                                                icon="mdi-eye"
                                                :href="route('admin.corporate-customers.show', customer.id)"
                                                @click.prevent="router.visit(route('admin.corporate-customers.show', customer.id))"
                                            />
                                            <v-btn
                                                size="small"
                                                color="primary"
                                                variant="text"
                                                icon="mdi-pencil"
                                                :href="route('admin.corporate-customers.edit', customer.id)"
                                                @click.prevent="router.visit(route('admin.corporate-customers.edit', customer.id))"
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
                                @update:model-value="(page) => router.visit(route('admin.corporate-customers.index', { page, search: search, category: categoryFilter }))"
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
                    Are you sure you want to delete {{ customerToDelete?.name }}?
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

