<script setup lang="ts">
import { onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { useTheme } from '@/composables/useTheme'

interface News {
  id: number
  title: string
  slug: string
  content: string
  cover_image: string | null
  published_at: string
}

interface Props {
  news: News
  relatedNews: News[]
}

defineProps<Props>()
const { applyTheme } = useTheme()

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

onMounted(() => {
  applyTheme()
})
</script>

<template>
  <PublicLayout :title="news.title">
    <v-container class="py-8">
      <!-- Back Button -->
      <v-row>
        <v-col cols="12">
          <Link href="/news" class="text-decoration-none">
            <v-btn
              variant="text"
              prepend-icon="mdi-arrow-left"
              class="mb-4"
            >
              Back to News
            </v-btn>
          </Link>
        </v-col>
      </v-row>

      <!-- Main Content -->
      <v-row justify="center">
        <v-col cols="12" lg="10">
          <v-card elevation="2">
            <!-- Cover Image -->
            <v-img
              v-if="news.cover_image"
              :src="news.cover_image"
              height="500"
              cover
              class="align-end"
            >
              <v-card-title class="text-h3 text-white pa-8" style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent)">
                {{ news.title }}
              </v-card-title>
            </v-img>

            <v-card-title v-else class="text-h3 pa-8">
              {{ news.title }}
            </v-card-title>

            <!-- Meta Information -->
            <v-card-subtitle class="px-8 py-4">
              <v-icon size="small" class="mr-2">mdi-calendar</v-icon>
              Published on {{ formatDate(news.published_at) }}
            </v-card-subtitle>

            <v-divider />

            <!-- Article Content -->
            <v-card-text class="px-8 py-6">
              <div class="text-body-1" style="line-height: 1.8;" v-html="news.content" />
            </v-card-text>


          </v-card>
        </v-col>
      </v-row>

      <!-- Related News -->
      <v-row v-if="relatedNews.length > 0" class="mt-8">
        <v-col cols="12">
          <h2 class="text-h4 mb-6">Related News</h2>
        </v-col>
        <v-col
          v-for="item in relatedNews"
          :key="item.id"
          cols="12"
          md="4"
        >
          <v-card elevation="2" hover class="h-100">
            <v-img
              :src="item.cover_image || '/images/placeholder-news.jpg'"
              height="200"
              cover
            />
            <v-card-title class="text-h6">
              {{ item.title }}
            </v-card-title>
            <v-card-subtitle>
              <v-icon size="small" start>mdi-calendar</v-icon>
              {{ formatDate(item.published_at) }}
            </v-card-subtitle>
            <v-card-actions>
              <Link :href="`/news/${item.slug}`" class="text-decoration-none">
                <v-btn
                  color="primary"
                  variant="text"
                  class="text-none"
                >
                  Read More
                  <v-icon end>mdi-arrow-right</v-icon>
                </v-btn>
              </Link>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </PublicLayout>
</template>

<style scoped>
/* Style for article content */
:deep(.text-body-1 h1),
:deep(.text-body-1 h2),
:deep(.text-body-1 h3) {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

:deep(.text-body-1 p) {
  margin-bottom: 1rem;
}

:deep(.text-body-1 ul),
:deep(.text-body-1 ol) {
  margin-bottom: 1rem;
  padding-left: 2rem;
}

:deep(.text-body-1 img) {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1.5rem 0;
}

:deep(.text-body-1 a) {
  color: var(--color-primary);
  text-decoration: underline;
}

:deep(.text-body-1 blockquote) {
  border-left: 4px solid var(--color-primary);
  padding-left: 1rem;
  margin: 1.5rem 0;
  font-style: italic;
  color: #666;
}
</style>

