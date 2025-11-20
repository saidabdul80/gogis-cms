<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

interface Customer {
    id: number
    name: string
    type: 'individual' | 'corporate'
    email?: string
    phone_number?: string
    customer_id: number
    customer_type: string
}

interface Props {
    modelValue?: Customer | null
    label?: string
    required?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    label: 'Select Customer',
    required: false
})

const emit = defineEmits<{
    'update:modelValue': [value: Customer | null]
}>()

const page = usePage()

const search = ref('')
const loading = ref(false)
const customers = ref<Customer[]>([])
const showCreateDialog = ref(false)
const customerType = ref<'individual' | 'corporate'>('individual')

// New customer form data
const newCustomer = ref({
    // Individual
    first_name: '',
    middle_name: '',
    last_name: '',
    nin: '',
    gender: '',
    dob: '',
    lga_id: null,
    // Corporate
    name: '',
    category: '',
    registration_number: '',
    registered_date: '',
    // Common
    email: '',
    phone_number: '',
    address: ''
})

const selectedCustomer = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value ?? null)
})

// Fetch customers based on search
const fetchCustomers = async (searchQuery: string) => {
    if (!searchQuery || searchQuery.length < 2) {
        customers.value = []
        return
    }

    loading.value = true
    try {
        const response = await fetch(`/admin/customers/search?q=${encodeURIComponent(searchQuery)}`)
        const data = await response.json()
        customers.value = data
    } catch (error) {
        console.error('Error fetching customers:', error)
        customers.value = []
    } finally {
        loading.value = false
    }
}

watch(search, (newValue) => {
    if (newValue && newValue.length >= 2) {
        fetchCustomers(newValue)
    } else {
        customers.value = []
    }
})

const openCreateDialog = (type: 'individual' | 'corporate') => {
    customerType.value = type
    showCreateDialog.value = true
    resetForm()
}

const resetForm = () => {
    newCustomer.value = {
        first_name: '',
        middle_name: '',
        last_name: '',
        nin: '',
        gender: '',
        dob: '',
        lga_id: null,
        name: '',
        category: '',
        registration_number: '',
        registered_date: '',
        email: '',
        phone_number: '',
        address: ''
    }
}

const createCustomer = async () => {
    const endpoint = customerType.value === 'individual'
        ? '/admin/individual-customers'
        : '/admin/corporate-customers'

    const formData = customerType.value === 'individual'
        ? {
            first_name: newCustomer.value.first_name,
            middle_name: newCustomer.value.middle_name,
            last_name: newCustomer.value.last_name,
            nin: newCustomer.value.nin,
            gender: newCustomer.value.gender,
            dob: newCustomer.value.dob,
            lga_id: newCustomer.value.lga_id,
            email: newCustomer.value.email,
            phone_number: newCustomer.value.phone_number,
            address: newCustomer.value.address
        }
        : {
            name: newCustomer.value.name,
            category: newCustomer.value.category,
            registration_number: newCustomer.value.registration_number,
            registered_date: newCustomer.value.registered_date,
            email: newCustomer.value.email,
            phone_number: newCustomer.value.phone_number,
            address: newCustomer.value.address
        }

    try {
        loading.value = true

        // Get CSRF token from Inertia shared props
        const csrfToken = page.props.csrf_token as string

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(formData)
        })

        if (!response.ok) {
            let errorMessage = `Server returned ${response.status}`
            try {
                const errorData = await response.json()
                console.error('Server error:', response.status, errorData)

                // Handle validation errors
                if (errorData.errors) {
                    const errors = Object.values(errorData.errors).flat()
                    errorMessage = errors.join('\n')
                } else if (errorData.message) {
                    errorMessage = errorData.message
                }
            } catch (e) {
                const errorText = await response.text()
                console.error('Server error (text):', response.status, errorText)
                errorMessage = errorText || errorMessage
            }
            throw new Error(errorMessage)
        }

        const data = await response.json()

        if (data.success && data.customer) {
            // Select the newly created customer
            selectedCustomer.value = data.customer
            showCreateDialog.value = false
            resetForm()
        } else {
            console.error('Unexpected response format:', data)
            throw new Error('Invalid response from server')
        }
    } catch (error: any) {
        console.error('Error creating customer:', error)
        alert(`Failed to create customer:\n${error.message || 'Please check the form and try again.'}`)
    } finally {
        loading.value = false
    }
}

const genderOptions = ['Male', 'Female']
const categoryOptions = ['Company', 'Hotel', 'Bank', 'Hospital', 'School', 'MDAs', 'Others']
</script>

<template>
    <div>
        <v-autocomplete
            variant="outlined"
            v-model="selectedCustomer"
            v-model:search="search"
            :items="customers"
            :loading="loading"
            :label="label"
            :required="required"
            item-title="name"
            item-value="id"
            return-object
            clearable
            no-filter
        >
            <template #item="{ props: itemProps, item }">
                <v-list-item v-bind="itemProps">
                    <template #prepend>
                        <v-icon :icon="item.raw.type === 'individual' ? 'mdi-account' : 'mdi-domain'" />
                    </template>
                    <!-- <v-list-item-title>{{ item.raw.name }}</v-list-item-title> -->
                    <v-list-item-subtitle>
                        {{ item.raw.type === 'individual' ? 'Individual' : 'Corporate' }}
                        <span v-if="item.raw.phone_number"> • {{ item.raw.phone_number }}</span>
                    </v-list-item-subtitle>
                </v-list-item>
            </template>

            <template #no-data>
                <v-list>
                    <v-list-item>
                        <v-list-item-title class="text-center text-grey">
                            {{ search && search.length >= 2 ? 'No customers found' : 'Type to search customers' }}
                        </v-list-item-title>
                    </v-list-item>
                    <v-divider v-if="search && search.length >= 2" />
                    <v-list-item
                        v-if="search && search.length >= 2"
                        @click="openCreateDialog('individual')"
                        class="text-primary"
                    >
                        <template #prepend>
                            <v-icon icon="mdi-plus-circle" color="primary" />
                        </template>
                        <v-list-item-title>Add Individual Customer</v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        v-if="search && search.length >= 2"
                        @click="openCreateDialog('corporate')"
                        class="text-primary"
                    >
                        <template #prepend>
                            <v-icon icon="mdi-plus-circle" color="primary" />
                        </template>
                        <v-list-item-title>Add Corporate Customer</v-list-item-title>
                    </v-list-item>
                </v-list>
            </template>
        </v-autocomplete>

        <!-- Create Customer Dialog -->
        <v-dialog v-model="showCreateDialog" max-width="600">
            <v-card>
                <v-card-title>
                    Add {{ customerType === 'individual' ? 'Individual' : 'Corporate' }} Customer
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="createCustomer">
                        <!-- Individual Customer Fields -->
                        <template v-if="customerType === 'individual'">
                            <v-row>
                                <v-col cols="12" md="4">
                                    <v-text-field
                                        v-model="newCustomer.first_name"
                                        label="First Name *"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-text-field
                                        v-model="newCustomer.middle_name"
                                        label="Middle Name"
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-text-field
                                        v-model="newCustomer.last_name"
                                        label="Last Name *"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="newCustomer.nin"
                                        label="NIN"
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="newCustomer.gender"
                                        :items="genderOptions"
                                        label="Gender"
                                    />
                                </v-col>
                            </v-row>
                        </template>

                        <!-- Corporate Customer Fields -->
                        <template v-else>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="newCustomer.name"
                                        label="Company Name *"
                                        required
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="newCustomer.category"
                                        :items="categoryOptions"
                                        label="Category"
                                    />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="newCustomer.registration_number"
                                        label="Registration Number *"
                                        required
                                    />
                                </v-col>
                            </v-row>
                        </template>

                        <!-- Common Fields -->
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="newCustomer.email"
                                    label="Email"
                                    type="email"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="newCustomer.phone_number"
                                    label="Phone Number"
                                />
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="showCreateDialog = false">Cancel</v-btn>
                    <v-btn color="primary" @click="createCustomer">Create</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>


