<template>
    <Head title="Edit Property Type" />

    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <div class="d-flex align-center mb-4">
                        <v-btn
                            icon="mdi-arrow-left"
                            variant="text"
                            @click="router.visit(route('admin.property-types.index'))"
                        />
                        <h1 class="text-h4 font-weight-bold ml-2">Edit Property Type</h1>
                    </div>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-text>
                            <v-form @submit.prevent="submit">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            v-model="form.name"
                                            label="Name *"
                                            variant="outlined"
                                            :error-messages="form.errors.name"
                                            required
                                        />
                                    </v-col>

                                    <v-col cols="12">
                                        <v-textarea
                                            v-model="form.description"
                                            label="Description"
                                            variant="outlined"
                                            rows="4"
                                            :error-messages="form.errors.description"
                                        />
                                    </v-col>
                                </v-row>

                                <v-divider class="my-4" />

                                <div class="d-flex justify-end gap-2">
                                    <v-btn
                                        variant="text"
                                        @click="router.visit(route('admin.property-types.index'))"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        :loading="form.processing"
                                    >
                                        Update Property Type
                                    </v-btn>
                                </div>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface PropertyType {
    id: number
    name: string
    description: string | null
}

interface Props {
    propertyType: PropertyType
}

const props = defineProps<Props>()

const form = useForm({
    name: props.propertyType.name,
    description: props.propertyType.description || '',
})

const submit = () => {
    form.put(route('admin.property-types.update', props.propertyType.id))
}
</script>

