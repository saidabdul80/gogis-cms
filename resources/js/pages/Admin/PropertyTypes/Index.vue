<template>
    <Head title="Property Types" />

    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <div class="d-flex justify-space-between align-center mb-4">
                        <h1 class="text-h4 font-weight-bold">Property Types</h1>
                        <v-btn
                            color="primary"
                            prepend-icon="mdi-plus"
                            @click="router.visit(route('admin.property-types.create'))"
                        >
                            Add Property Type
                        </v-btn>
                    </div>
                </v-col>
            </v-row>

            <!-- Search -->
            <v-row>
                <v-col cols="12" md="4">
                    <v-text-field
                        v-model="search"
                        label="Search property types..."
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        density="compact"
                        clearable
                        hide-details
                    />
                </v-col>
            </v-row>

            <!-- Table -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-table>
                            <thead>
                                <tr>
                                    <th class="text-left">Name</th>
                                    <th class="text-left">Description</th>
                                    <th class="text-left">Properties Count</th>
                                    <th class="text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="propertyType in propertyTypes.data" :key="propertyType.id">
                                    <td class="font-weight-medium">{{ propertyType.name }}</td>
                                    <td>{{ propertyType.description || 'N/A' }}</td>
                                    <td>{{ propertyType.properties_count || 0 }}</td>
                                    <td>
                                        <v-btn
                                            icon="mdi-pencil"
                                            size="small"
                                            variant="text"
                                            @click="router.visit(route('admin.property-types.edit', propertyType.id))"
                                        />
                                        <v-btn
                                            icon="mdi-delete"
                                            size="small"
                                            variant="text"
                                            color="error"
                                            @click="deletePropertyType(propertyType)"
                                        />
                                    </td>
                                </tr>
                                <tr v-if="propertyTypes.data.length === 0">
                                    <td colspan="4" class="text-center py-8 text-grey">
                                        No property types found
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>

                        <!-- Pagination -->
                        <v-divider />
                        <div class="d-flex justify-center py-4">
                            <v-pagination
                                v-if="propertyTypes.last_page > 1"
                                :length="propertyTypes.last_page"
                                :model-value="propertyTypes.current_page"
                                @update:model-value="(page) => router.visit(route('admin.property-types.index', { page, search }))"
                            />
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { watchDebounced } from '@vueuse/core'

interface PropertyType {
    id: number
    name: string
    description: string | null
    properties_count?: number
}

interface Props {
    propertyTypes: {
        data: PropertyType[]
        current_page: number
        last_page: number
    }
    filters: {
        search: string | null
    }
}

const props = defineProps<Props>()

const search = ref(props.filters.search || '')

// Watch for search changes with debounce
watchDebounced(
    search,
    () => {
        router.get(
            route('admin.property-types.index'),
            { search: search.value },
            {
                preserveState: true,
                replace: true,
            }
        )
    },
    { debounce: 300 }
)

const deletePropertyType = (propertyType: PropertyType) => {
    if (confirm(`Are you sure you want to delete "${propertyType.name}"?`)) {
        router.delete(route('admin.property-types.destroy', propertyType.id))
    }
}
</script>

