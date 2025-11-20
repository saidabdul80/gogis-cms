<script setup lang="ts">
import { ref } from 'vue'
import { watchDebounced } from '@vueuse/core'
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
    filters: {
        search?: string
        status?: string
    }
}

const props = defineProps<Props>()

const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const deleteDialog = ref(false)
const newsToDelete = ref<News | null>(null)

const statusOptions = [
    { title: 'All', value: '' },
    { title: 'Published', value: 'published' },
    { title: 'Draft', value: 'draft' }
]

watchDebounced([search, statusFilter], () => {
    router.get(route('admin.news.index'), {
        search: search.value,
        status: statusFilter.value
    }, {
        preserveState: true,
        replace: true
    })
}, { debounce: 300 })

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
        router.delete(route('admin.news.destroy', newsToDelete.value.slug), {
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
                            <!-- Filters -->
                            <v-row class="mb-4">
                                <v-col cols="12" md="8">
                                    <v-text-field
                                        v-model="search"
                                        prepend-inner-icon="mdi-magnify"
                                        label="Search news..."
                                        placeholder="Search by title, content, or slug"
                                        clearable
                                    />
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-select
                                        v-model="statusFilter"
                                        :items="statusOptions"
                                        label="Status"
                                    />
                                </v-col>
                            </v-row>

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
                                            <Link :href="route('admin.news.edit', item.slug)">
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

                            <v-pagination
                                v-if="news.last_page > 1"
                                :length="news.last_page"
                                :model-value="news.current_page"
                                @update:model-value="(page) => router.visit(route('admin.news.index', { page, search: search, status: statusFilter }))"
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

