<script setup lang="ts">
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import RichTextEditor from '@/components/RichTextEditor.vue'

const form = useForm({
    title: '',
    content: '',
    cover_image: null as File | null,
    published_at: null as string | null,
    post_to_social_media: false,
})

const imagePreview = ref<string | null>(null)
const publishNow = ref(false)

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    
    if (file) {
        form.cover_image = file
        
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string
        }
        reader.readAsDataURL(file)
    }
}

const removeImage = () => {
    form.cover_image = null
    imagePreview.value = null
}

const handlePublishNowChange = (value: boolean) => {
    if (value) {
        form.published_at = new Date().toISOString().slice(0, 16)
    } else {
        form.published_at = null
    }
}

const submit = () => {
    form.post(route('admin.news.store'), {
        preserveScroll: true,
        forceFormData: true,
    })
}
</script>

<template>
    <AdminLayout title="Create News Article">
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex align-center">
                            <Link :href="route('admin.news.index')">
                                <v-btn icon="mdi-arrow-left" variant="text" class="mr-2" />
                            </Link>
                            <v-icon class="mr-2">mdi-newspaper-plus</v-icon>
                            <span>Create News Article</span>
                        </v-card-title>

                        <v-card-text>
                            <v-form @submit.prevent="submit">
                                <v-row>
                                    <v-col cols="12" md="8">
                                        <v-text-field
                                            v-model="form.title"
                                            label="Title"
                                            :error-messages="form.errors.title"
                                            variant="outlined"
                                            required
                                        />

                                        <RichTextEditor
                                            v-model="form.content"
                                            label="Content"
                                            :error-messages="form.errors.content"
                                            class="mb-4"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="4">
                                        <v-card variant="outlined" class="mb-4">
                                            <v-card-title class="text-subtitle-1">
                                                <v-icon class="mr-2">mdi-image</v-icon>
                                                Cover Image
                                            </v-card-title>
                                            <v-card-text>
                                                <div v-if="imagePreview" class="mb-3">
                                                    <v-img
                                                        :src="imagePreview"
                                                        height="200"
                                                        cover
                                                        class="rounded"
                                                    />
                                                    <v-btn
                                                        color="error"
                                                        size="small"
                                                        class="mt-2"
                                                        @click="removeImage"
                                                    >
                                                        Remove Image
                                                    </v-btn>
                                                </div>
                                                <v-file-input
                                                    v-else
                                                    label="Upload Cover Image"
                                                    accept="image/*"
                                                    prepend-icon="mdi-camera"
                                                    variant="outlined"
                                                    density="compact"
                                                    :error-messages="form.errors.cover_image"
                                                    @change="handleImageChange"
                                                />
                                            </v-card-text>
                                        </v-card>

                                        <v-card variant="outlined" class="mb-4">
                                            <v-card-title class="text-subtitle-1">
                                                <v-icon class="mr-2">mdi-calendar</v-icon>
                                                Publishing
                                            </v-card-title>
                                            <v-card-text>
                                                <v-switch
                                                    v-model="publishNow"
                                                    label="Publish Now"
                                                    color="primary"
                                                    hide-details
                                                    @update:model-value="handlePublishNowChange"
                                                />

                                                <v-text-field
                                                    v-if="!publishNow"
                                                    v-model="form.published_at"
                                                    label="Schedule Publication"
                                                    type="datetime-local"
                                                    variant="outlined"
                                                    density="compact"
                                                    :error-messages="form.errors.published_at"
                                                    class="mt-3"
                                                    hint="Leave empty to save as draft"
                                                    persistent-hint
                                                />
                                            </v-card-text>
                                        </v-card>

                                        <v-card variant="outlined">
                                            <v-card-title class="text-subtitle-1">
                                                <v-icon class="mr-2">mdi-share-variant</v-icon>
                                                Social Media
                                            </v-card-title>
                                            <v-card-text>
                                                <v-switch
                                                    v-model="form.post_to_social_media"
                                                    label="Post to Social Media"
                                                    color="primary"
                                                    hide-details
                                                    hint="Automatically post to configured social media platforms"
                                                    persistent-hint
                                                />
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </v-row>

                                <v-divider class="my-4" />

                                <div class="d-flex justify-end ga-2">
                                    <Link :href="route('admin.news.index')">
                                        <v-btn variant="text">Cancel</v-btn>
                                    </Link>
                                    <v-btn
                                        type="submit"
                                        color="primary"
                                        :loading="form.processing"
                                        prepend-icon="mdi-content-save"
                                    >
                                        Create Article
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

