<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { useForm, router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    title: '',
    description: '',
    image: null as File | null,
    button_text: '',
    button_link: '',
    order: 0,
    is_active: true,
})

const imagePreview = ref<string | null>(null)

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    
    if (file) {
        form.image = file
        
        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string
        }
        reader.readAsDataURL(file)
    }
}

const submit = () => {
    // Create FormData manually
    const formData = new FormData()

    formData.append('title', form.title)
    formData.append('description', form.description)
    formData.append('button_text', form.button_text)
    formData.append('button_link', form.button_link)
    formData.append('order', String(form.order))
    formData.append('is_active', form.is_active ? '1' : '0')

    if (form.image) {
        formData.append('image', form.image)
    }

    router.post(route('admin.carousel.store'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            imagePreview.value = null
        },
    })
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <div class="d-flex align-center mb-6">
                        <Link :href="route('admin.carousel.index')">
                            <v-btn
                                icon="mdi-arrow-left"
                                variant="text"
                                class="mr-4"
                            />
                        </Link>
                        <div>
                            <h1 class="text-h4 font-weight-bold">Create Carousel Slide</h1>
                            <p class="text-body-2 text-medium-emphasis mt-1">
                                Add a new slide to the homepage carousel
                            </p>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-text>
                            <v-form @submit.prevent="submit">
                                <v-text-field
                                    v-model="form.title"
                                    label="Title *"
                                    :error-messages="form.errors.title"
                                    variant="outlined"
                                    class="mb-4"
                                />

                                <v-textarea
                                    v-model="form.description"
                                    label="Description"
                                    :error-messages="form.errors.description"
                                    variant="outlined"
                                    rows="3"
                                    class="mb-4"
                                />

                                <v-file-input
                                    label="Image *"
                                    accept="image/*"
                                    :error-messages="form.errors.image"
                                    variant="outlined"
                                    prepend-icon=""
                                    prepend-inner-icon="mdi-image"
                                    class="mb-4"
                                    @change="handleImageChange"
                                />

                                <v-img
                                    v-if="imagePreview"
                                    :src="imagePreview"
                                    max-height="300"
                                    class="mb-4 rounded"
                                />

                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.button_text"
                                            label="Button Text"
                                            :error-messages="form.errors.button_text"
                                            variant="outlined"
                                            placeholder="e.g., Learn More"
                                        />
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            v-model="form.button_link"
                                            label="Button Link"
                                            :error-messages="form.errors.button_link"
                                            variant="outlined"
                                            placeholder="e.g., /about"
                                        />
                                    </v-col>
                                </v-row>

                                <v-text-field
                                    v-model.number="form.order"
                                    label="Order *"
                                    type="number"
                                    :error-messages="form.errors.order"
                                    variant="outlined"
                                    hint="Lower numbers appear first"
                                    persistent-hint
                                    class="mb-4"
                                />

                                <v-switch
                                    v-model="form.is_active"
                                    label="Active"
                                    color="primary"
                                    :error-messages="form.errors.is_active"
                                    hide-details
                                />

                                <v-divider class="my-6" />

                                <div class="d-flex gap-3">
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                    >
                                        Create Slide
                                    </v-btn>
                                    <Link :href="route('admin.carousel.index')">
                                        <v-btn variant="outlined">
                                            Cancel
                                        </v-btn>
                                    </Link>
                                </div>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

