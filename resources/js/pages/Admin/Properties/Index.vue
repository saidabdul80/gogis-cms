<script setup lang="ts">
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface PropertyType {
    id: number
    name: string
    description: string | null
}

interface Property {
    id: number
    property_type_id?: number
    property_type?: PropertyType
    address: string
    longitude?: number
    latitude?: number
    description?: string
    number_type: 'GSL' | 'GM_Number'
    number_value: string
    start_date?: string
    payment_frequency?: string
    amount?: number
    customer_id?: number
    customer_type?: string
    customer?: any
    formatted_number?: string
    created_at: string
}

interface Props {
    properties: {
        data: Property[]
        current_page: number
        last_page: number
        per_page: number
        total: number
    }
    propertyTypes: PropertyType[]
    filters: {
        search?: string
        number_type?: string
        property_type_id?: string
    }
}

const props = defineProps<Props>()

const deleteDialog = ref(false)
const propertyToDelete = ref<Property | null>(null)

const search = ref(props.filters.search || '')
const numberTypeFilter = ref(props.filters.number_type || '')
const propertyTypeFilter = ref(props.filters.property_type_id || '')

const numberTypeOptions = [
    { title: 'All', value: '' },
    { title: 'GSL', value: 'GSL' },
    { title: 'GM Number', value: 'GM_Number' }
]

const propertyTypeOptions = [
    { title: 'All Types', value: '', id: '' },
    ...props.propertyTypes.map(pt => ({ title: pt.name, value: pt.id.toString(), id: pt.id }))
]

watch([search, numberTypeFilter, propertyTypeFilter], () => {
    router.get(route('admin.properties.index'), {
        search: search.value,
        number_type: numberTypeFilter.value,
        property_type_id: propertyTypeFilter.value
    }, {
        preserveState: true,
        replace: true
    })
}, { debounce: 300 })

const openDeleteDialog = (property: Property) => {
    propertyToDelete.value = property
    deleteDialog.value = true
}

const confirmDelete = () => {
    if (propertyToDelete.value) {
        router.delete(route('admin.properties.destroy', propertyToDelete.value.id), {
            onSuccess: () => {
                deleteDialog.value = false
                propertyToDelete.value = null
            }
        })
    }
}

const formatCurrency = (amount?: number) => {
    if (!amount) return 'N/A'
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN'
    }).format(amount)
}

const formatPaymentFrequency = (frequency?: string) => {
    if (!frequency) return 'N/A'
    return frequency.charAt(0).toUpperCase() + frequency.slice(1)
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Property Profiling</span>
                            <Link :href="route('admin.properties.create')">
                                <v-btn color="primary" prepend-icon="mdi-plus">
                                    Add Property
                                </v-btn>
                            </Link>
                        </v-card-title>

                        <v-card-text>
                            <!-- Filters -->
                            <v-row class="mb-4">
                                <v-col cols="12" md="4">
                                    <v-text-field
                                        v-model="search"
                                        label="Search"
                                        prepend-inner-icon="mdi-magnify"
                                        placeholder="Search by number, address, or description"
                                        clearable
                                        variant="outlined"
                                        density="compact"
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-select
                                        v-model="propertyTypeFilter"
                                        :items="propertyTypeOptions"
                                        label="Property Type"
                                        variant="outlined"
                                        density="compact"
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-select
                                        v-model="numberTypeFilter"
                                        :items="numberTypeOptions"
                                        label="Number Type"
                                        variant="outlined"
                                        density="compact"
                                    />
                                </v-col>
                            </v-row>

                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Number</th>
                                        <th>Customer</th>
                                        <th>Property Type</th>
                                        <th>Address</th>
                                        <th>Amount</th>
                                        <th>Payment Frequency</th>
                                        <th>Start Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="property in properties.data" :key="property.id">
                                        <td>
                                            <v-chip :color="property.number_type === 'GSL' ? 'primary' : 'secondary'" size="small">
                                                {{ property.number_type }}
                                            </v-chip>
                                            {{ property.number_value }}
                                        </td>
                                        <td>
                                            <div v-if="property.customer" class="d-flex align-center">
                                                <v-icon
                                                    :icon="property.customer_type?.includes('Individual') ? 'mdi-account' : 'mdi-domain'"
                                                    size="small"
                                                    class="mr-2"
                                                />
                                                <div>
                                                    <div class="text-body-2">
                                                        {{ property.customer.name || `${property.customer.first_name} ${property.customer.last_name}` }}
                                                    </div>
                                                    <div class="text-caption text-grey">
                                                        {{ property.customer_type?.includes('Individual') ? 'Individual' : 'Corporate' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <span v-else class="text-grey">No customer</span>
                                        </td>
                                        <td>
                                            <v-chip v-if="property.property_type" color="info" size="small" variant="tonal">
                                                {{ property.property_type.name }}
                                            </v-chip>
                                            <span v-else class="text-grey">N/A</span>
                                        </td>
                                        <td>{{ property.address }}</td>
                                        <td>{{ formatCurrency(property.amount) }}</td>
                                        <td>{{ formatPaymentFrequency(property.payment_frequency) }}</td>
                                        <td>{{ property.start_date || 'N/A' }}</td>
                                        <td>
                                            <v-btn
                                                size="small"
                                                color="primary"
                                                variant="text"
                                                icon="mdi-pencil"
                                                :href="route('admin.properties.edit', property.id)"
                                                @click.prevent="router.visit(route('admin.properties.edit', property.id))"
                                            />
                                            <v-btn
                                                size="small"
                                                color="error"
                                                variant="text"
                                                icon="mdi-delete"
                                                @click="openDeleteDialog(property)"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>

                            <v-pagination
                                v-if="properties.last_page > 1"
                                :length="properties.last_page"
                                :model-value="properties.current_page"
                                @update:model-value="(page) => router.visit(route('admin.properties.index', { page, search: search, number_type: numberTypeFilter }))"
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
                    Are you sure you want to delete property {{ propertyToDelete?.number_type }}: {{ propertyToDelete?.number_value }}?
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

