<script setup lang="ts">
import { onMounted } from 'vue'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { useTheme } from '@/composables/useTheme'

interface Props {
  page: {
    title: string
    content: string
  }
  settings: {
    site_name: string
    primary_color: string
    secondary_color: string
    accent_color: string
    dg_name?: string
    dg_title?: string
    dg_bio?: string
    dg_image?: string
  }
}

defineProps<Props>()
const { applyTheme } = useTheme()

onMounted(() => {
  applyTheme()
})
</script>

<template>
  <PublicLayout title="About Us">
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
                About {{ settings.site_name }}
              </h1>
              <p class="text-h6 text-white" style="opacity: 0.9;">
                Learn more about our mission, vision, and the team behind GOGIS
              </p>
            </v-col>
          </v-row>
        </v-container>
      </v-sheet>
    </v-container>

    <!-- About Content -->
    <v-container class="py-12">
      <v-row justify="center">
        <v-col cols="12" md="10">
          <v-card elevation="2" class="pa-8">
            <div v-html="page.content" class="text-body-1"></div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Director General Section -->
      <v-row v-if="settings.dg_name" justify="center" class="mt-12">
        <v-col cols="12" md="10">
          <v-card elevation="2">
            <v-card-title class="text-h4 pa-6" :style="{ color: settings.primary_color }">
              Director General
            </v-card-title>
            <v-divider />
            <v-card-text class="pa-6">
              <v-row align="center">
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar size="200" class="mb-4">
                    <v-img :src="settings.dg_image || '/images/placeholder.jpg'" :alt="settings.dg_name" />
                  </v-avatar>
                  <h3 class="text-h5 font-weight-bold">{{ settings.dg_name }}</h3>
                  <p class="text-subtitle-1" :style="{ color: settings.accent_color }">
                    {{ settings.dg_title }}
                  </p>
                </v-col>
                <v-col cols="12" md="8">
                  <p class="text-body-1">{{ settings.dg_bio }}</p>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </PublicLayout>
</template>

