<script setup lang="ts">
import { onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { useTheme } from '@/composables/useTheme'

interface News {
  id: number
  title: string
  slug: string
  excerpt: string
  cover_image: string | null
  published_at: string
}

interface Props {
  news: News[]
  settings: {
    site_name: string
    primary_color: string
    secondary_color: string
    accent_color: string
  }
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
  <PublicLayout title="News">
    <!-- Hero Section -->
    <v-container fluid class="pa-0">
      <v-sheet
        :style="{ 
          background: `linear-gradient(135deg, ${settings.primary_color} 0%, ${settings.accent_color} 100%)`,
          minHeight: '300px'
        }"
        class="d-flex align-center"
      >
        <v-container>
          <v-row justify="center">
            <v-col cols="12" md="8" class="text-center">
              <h1 class="text-h2 text-md-h1 font-weight-bold text-white mb-4">
                Latest News
              </h1>
              <p class="text-h6 text-white" style="opacity: 0.9;">
                Stay updated with the latest news and announcements from {{ settings.site_name }}
              </p>
            </v-col>
          </v-row>
        </v-container>
      </v-sheet>
    </v-container>

    <!-- News Grid -->
    <v-container class="py-12">
      <v-row v-if="news.length > 0">
        <v-col
          v-for="item in news"
          :key="item.id"
          cols="12"
          md="6"
          lg="4"
        >
          <v-card elevation="2" class="h-100 d-flex flex-column" hover>
            <v-img
              :src="item.cover_image || '/images/placeholder-news.jpg'"
              height="250"
              cover
            />
            <v-card-title class="text-h6">
              {{ item.title }}
            </v-card-title>
            <v-card-subtitle>
              <v-icon size="small" start>mdi-calendar</v-icon>
              {{ formatDate(item.published_at) }}
            </v-card-subtitle>
            <v-card-text class="flex-grow-1">
              {{ item.excerpt }}
            </v-card-text>
            <v-card-actions>
              <Link :href="`/news/${item.slug}`" class="text-decoration-none">
                <v-btn
                  :color="settings.primary_color"
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

      <!-- Empty State -->
      <v-row v-else justify="center">
        <v-col cols="12" md="6" class="text-center">
          <v-icon size="100" :color="settings.primary_color">mdi-newspaper-variant-outline</v-icon>
          <h3 class="text-h5 mt-4 mb-2">No News Available</h3>
          <p class="text-body-1">Check back later for updates and announcements.</p>
        </v-col>
      </v-row>
    </v-container>
  </PublicLayout>
</template>

