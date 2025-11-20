<script setup lang="ts">
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import GChip from '@/components/GChip.vue'

interface News {
    id: number
    title: string
    slug: string
    content: string
    cover_image: string | null
    published_at: string | null
    created_at: string
    updated_at: string
}

interface Props {
    news: {
        data: News[]
        current_page: number
        last_page: number
        per_page: number
        total: number
    }
}

const props = defineProps<Props>()

const search = ref('')
const deleteDialog = ref(false)
const newsToDelete = ref<News | null>(null)

const formatDate = (date: string | null) => {
    if (!date) return 'Draft'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const isPublished = (news: News) => {
    return news.published_at && new Date(news.published_at) <= new Date()
}

const confirmDelete = (news: News) => {
    newsToDelete.value = news
    deleteDialog.value = true
}

const deleteNews = () => {
    if (newsToDelete.value) {
        router.delete(route('admin.news.destroy', newsToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                deleteDialog.value = false
                newsToDelete.value = null
            }
        })
    }
}

const stripHtml = (html: string) => {
    const tmp = document.createElement('DIV')
    tmp.innerHTML = html
    return tmp.textContent || tmp.innerText || ''
}
</script>

<template>
    <AdminLayout title="News Management">
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex align-center justify-space-between">
                            <div class="d-flex align-center">
                                <v-icon class="mr-2">mdi-newspaper</v-icon>
                                <span class="text-h6">News Articles</span>
                                <GChip class="ml-2" size="small">{{ news.total }} articles</GChip>
                            </div>
                            <Link :href="route('admin.news.create')">
                                <v-btn color="primary" prepend-icon="mdi-plus">
                                    Add New Article
                                </v-btn>
                            </Link>
                        </v-card-title>

                        <v-card-text>
                            <v-text-field
                                v-model="search"
                                prepend-inner-icon="mdi-magnify"
                                label="Search news..."
                                variant="outlined"
                                density="compact"
                                hide-details
                                class="mb-4"
                            />

                            <v-list lines="three">
                                <v-list-item
                                    v-for="item in news.data"
                                    :key="item.id"
                                    class="mb-2 border !border-gray-200 rounded"
                                >
                                    <template #prepend>
                                        <v-avatar size="80" rounded="lg">
                                            <v-img
                                                v-if="item.cover_image"
                                                :src="item.cover_image"
                                                cover
                                            />
                                            <v-icon v-else size="40">mdi-newspaper</v-icon>
                                        </v-avatar>
                                    </template>

                                    <v-list-item-title class="text-h6 mb-1">
                                        {{ item.title }}
                                        <GChip
                                            :color="isPublished(item) ? 'success' : 'grey'"
                                            size="small"
                                            class="ml-2"
                                        >
                                            {{ isPublished(item) ? 'Published' : 'Draft' }}
                                        </GChip>
                                    </v-list-item-title>

                                    <v-list-item-subtitle class="mb-2">
                                        <v-icon size="small" class="mr-1">mdi-calendar</v-icon>
                                        {{ formatDate(item.published_at) }}
                                        <span class="mx-2">•</span>
                                        <v-icon size="small" class="mr-1">mdi-clock-outline</v-icon>
                                        Updated {{ formatDate(item.updated_at) }}
                                    </v-list-item-subtitle>

                                    <v-list-item-subtitle class="text-caption">
                                        {{ stripHtml(item.content).substring(0, 150) }}...
                                    </v-list-item-subtitle>

                                    <template #append>
                                        <div class="d-flex flex-column ga-2">
                                            <Link :href="route('admin.news.edit', item.id)">
                                                <v-btn
                                                    icon="mdi-pencil"
                                                    size="small"
                                                    variant="text"
                                                    color="primary"
                                                />
                                            </Link>
                                            <v-btn
                                                icon="mdi-delete"
                                                size="small"
                                                variant="text"
                                                color="error"
                                                @click="confirmDelete(item)"
                                            />
                                        </div>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h6">Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete "{{ newsToDelete?.title }}"? This action cannot be undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="deleteDialog = false">Cancel</v-btn>
                    <v-btn color="error" @click="deleteNews">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AdminLayout>
</template>

