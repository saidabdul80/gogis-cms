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

interface Event {
  id: number
  title: string
  slug: string
  description: string
  start_date: string
  end_date: string | null
  location: string
  cover_image: string | null
}

interface CarouselSlide {
  id: number
  title: string
  description: string | null
  image: string
  button_text: string | null
  button_link: string | null
  order: number
  is_active: boolean
}

interface Props {
  latestNews: News[]
  upcomingEvents: Event[]
  carouselEnabled: boolean
  carouselSlides: CarouselSlide[]
  settings: {
    site_name: string
    site_tagline: string
    site_description: string
    primary_color: string
    secondary_color: string
    accent_color: string
  }
}

const props = defineProps<Props>()
const { applyTheme } = useTheme()

onMounted(() => {
  applyTheme()
})
</script>

<template>
  <PublicLayout title="Home">
    <!-- Carousel Section (when enabled) -->
    <v-container v-if="carouselEnabled && carouselSlides.length > 0" fluid class="pa-0">
      <v-carousel
        height="600"
        cycle
        hide-delimiter-background
        show-arrows="hover"
      >
        <v-carousel-item
          v-for="slide in carouselSlides"
          :key="slide.id"
          :src="slide.image"
          cover
        >
          <div class="carousel-overlay d-flex align-center justify-center">
            <v-container>
              <v-row align="center" justify="center">
                <v-col cols="12" md="8" class="text-center">
                  <v-fade-transition appear>
                    <div>
                      <h1 class="text-h2 text-md-h1 font-weight-bold text-white mb-4">
                        {{ slide.title }}
                      </h1>
                      <p v-if="slide.description" class="text-h5 text-md-h4 text-white mb-6" style="opacity: 0.95;">
                        {{ slide.description }}
                      </p>
                      <v-btn
                        v-if="slide.button_text && slide.button_link"
                        :href="slide.button_link"
                        size="x-large"
                        :color="settings.secondary_color"
                        elevation="8"
                        class="text-none px-8"
                      >
                        {{ slide.button_text }}
                      </v-btn>
                    </div>
                  </v-fade-transition>
                </v-col>
              </v-row>
            </v-container>
          </div>
        </v-carousel-item>
      </v-carousel>
    </v-container>

    <!-- Default Hero Section (when carousel is disabled) -->
    <v-container v-else fluid class="pa-0">
      <v-sheet
        :style="{
          background: `linear-gradient(135deg, ${settings.primary_color}dd 0%, ${settings.accent_color}dd 100%)`,
          minHeight: '600px',
          position: 'relative',
          overflow: 'hidden'
        }"
        class="d-flex align-center"
      >
        <!-- Decorative Background Pattern -->
        <div
          style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;"
        >
          <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
          </svg>
        </div>

        <v-container style="position: relative; z-index: 1;">
          <v-row align="center" justify="center">
            <v-col cols="12" md="8" class="text-center">
              <v-fade-transition appear>
                <div>
                  <h1 class="text-h2 text-md-h1 font-weight-bold text-white mb-4">
                    {{ settings.site_name }}
                  </h1>
                  <p class="text-h5 text-md-h4 text-white mb-6" style="opacity: 0.95;">
                    {{ settings.site_tagline }}
                  </p>
                  <p class="text-h6 text-white mb-8" style="opacity: 0.9;">
                    {{ settings.site_description }}
                  </p>
                  <div class="d-flex flex-wrap justify-center ga-4">
                    <v-btn
                      href="/about"
                      size="x-large"
                      :color="settings.secondary_color"
                      elevation="8"
                      class="text-none px-8"
                    >
                      <v-icon start>mdi-information</v-icon>
                      Learn More
                    </v-btn>
                    <v-btn
                      href="/contact"
                      size="x-large"
                      variant="outlined"
                      color="white"
                      class="text-none px-8"
                    >
                      <v-icon start>mdi-email</v-icon>
                      Contact Us
                    </v-btn>
                  </div>
                </div>
              </v-fade-transition>
            </v-col>
          </v-row>
        </v-container>

        <!-- Decorative Wave -->
        <div style="position: absolute; bottom: -1px; left: 0; right: 0;">
          <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
          </svg>
        </div>
      </v-sheet>
    </v-container>

    <!-- About GOGIS Section -->
    <v-container class="py-16" v-if="$page.props.appSettings.aboutBackground || $page.props.appSettings.aboutObjective || $page.props.appSettings.aboutTimeline">
      <v-row>
        <v-col cols="12">
          <div class="text-center mb-12">
            <h2 class="text-h3 font-weight-bold mb-4" :style="{ color: settings.primary_color }">
              About GOGIS
            </h2>
            <p class="text-h6 text-grey-darken-1">
              Gombe State Geographic Information Systems
            </p>
          </div>
        </v-col>
      </v-row>

      <v-row>
        <!-- Background Section -->
        <v-col cols="12" v-if="$page.props.appSettings.aboutBackground">
          <v-card elevation="2" class="pa-6 mb-6">
            <div class="d-flex align-center mb-4">
              <v-icon :color="settings.primary_color" size="32" class="mr-3">mdi-information-outline</v-icon>
              <h3 class="text-h5 font-weight-bold" :style="{ color: settings.primary_color }">
                1. Background
              </h3>
            </div>
            <div class="about-content text-body-1 text-grey-darken-2" v-html="$page.props.appSettings.aboutBackground"></div>
          </v-card>
        </v-col>

        <!-- Objective Section -->
        <v-col cols="12" v-if="$page.props.appSettings.aboutObjective">
          <v-card elevation="2" class="pa-6 mb-6">
            <div class="d-flex align-center mb-4">
              <v-icon :color="settings.primary_color" size="32" class="mr-3">mdi-target</v-icon>
              <h3 class="text-h5 font-weight-bold" :style="{ color: settings.primary_color }">
                2. Objective
              </h3>
            </div>
            <div class="about-content text-body-1 text-grey-darken-2" v-html="$page.props.appSettings.aboutObjective"></div>
          </v-card>
        </v-col>

        <!-- Timeline Section -->
        <v-col cols="12" v-if="$page.props.appSettings.aboutTimeline">
          <v-card elevation="2" class="pa-6">
            <div class="d-flex align-center mb-4">
              <v-icon :color="settings.primary_color" size="32" class="mr-3">mdi-clock-outline</v-icon>
              <h3 class="text-h5 font-weight-bold" :style="{ color: settings.primary_color }">
                3. Timeline
              </h3>
            </div>
            <div class="about-content text-body-1 text-grey-darken-2" v-html="$page.props.appSettings.aboutTimeline"></div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Latest News Section -->
    <v-container class="py-16">
      <div class="text-center mb-12">
        <h2 class="text-h3 font-weight-bold mb-4" :style="{ color: settings.primary_color }">
          Latest News
        </h2>
        <p class="text-h6 text-grey-darken-1">
          Stay updated with the latest developments and announcements
        </p>
      </div>

      <v-row>
        <v-col
          v-for="news in latestNews.slice(0, 3)"
          :key="news.id"
          cols="12"
          md="4"
        >
          <v-card
            :href="`/news/${news.slug}`"
            hover
            class="h-100"
            elevation="4"
          >
            <v-img
              v-if="news.cover_image"
              :src="news.cover_image"
              height="200"
              cover
            />
            <v-sheet
              v-else
              height="200"
              :color="settings.primary_color"
              class="d-flex align-center justify-center"
            >
              <v-icon size="64" color="white">mdi-newspaper</v-icon>
            </v-sheet>

            <v-card-title class="text-h6">
              {{ news.title }}
            </v-card-title>

            <v-card-text>
              <div class="text-caption text-grey mb-2">
                {{ new Date(news.published_at).toLocaleDateString() }}
              </div>
              <div v-html="news.content.substring(0, 150) + '...'" />
            </v-card-text>

            <v-card-actions>
              <v-btn
                :color="settings.primary_color"
                variant="text"
                class="text-none"
              >
                Read More
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <div class="text-center mt-8">
        <v-btn
          href="/news"
          size="large"
          :color="settings.primary_color"
          variant="outlined"
          class="text-none"
        >
          View All News
          <v-icon end>mdi-arrow-right</v-icon>
        </v-btn>
      </div>
    </v-container>

    <!-- Upcoming Events Section -->
    <v-sheet :color="settings.primary_color + '10'" class="py-16">
      <v-container>
        <div class="text-center mb-12">
          <h2 class="text-h3 font-weight-bold mb-4" :style="{ color: settings.primary_color }">
            Upcoming Events
          </h2>
          <p class="text-h6 text-grey-darken-1">
            Join us at our upcoming events and activities
          </p>
        </div>

        <v-row>
          <v-col
            v-for="event in upcomingEvents.slice(0, 4)"
            :key="event.id"
            cols="12"
            sm="6"
            md="3"
          >
            <v-card
              :href="`/events/${event.slug}`"
              hover
              class="h-100"
              elevation="4"
            >
              <v-img
                v-if="event.cover_image"
                :src="event.cover_image"
                height="180"
                cover
              />
              <v-sheet
                v-else
                height="180"
                :color="settings.accent_color"
                class="d-flex align-center justify-center"
              >
                <v-icon size="64" color="white">mdi-calendar</v-icon>
              </v-sheet>

              <v-card-title class="text-subtitle-1">
                {{ event.title }}
              </v-card-title>

              <v-card-text>
                <div class="d-flex align-center mb-2">
                  <v-icon size="small" :color="settings.primary_color" class="mr-2">mdi-calendar</v-icon>
                  <span class="text-caption">{{ new Date(event.start_date).toLocaleDateString() }}</span>
                </div>
                <div class="d-flex align-center">
                  <v-icon size="small" :color="settings.primary_color" class="mr-2">mdi-map-marker</v-icon>
                  <span class="text-caption">{{ event.location }}</span>
                </div>
              </v-card-text>

              <v-card-actions>
                <v-btn
                  :color="settings.primary_color"
                  variant="text"
                  size="small"
                  class="text-none"
                >
                  View Details
                  <v-icon end size="small">mdi-arrow-right</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>

        <div class="text-center mt-8">
          <v-btn
            href="/events"
            size="large"
            :color="settings.primary_color"
            class="text-none"
          >
            View All Events
            <v-icon end>mdi-arrow-right</v-icon>
          </v-btn>
        </div>
      </v-container>
    </v-sheet>

    <!-- Call to Action Section -->
    <v-container class="py-16">
      <v-card
        :style="{
          background: `linear-gradient(135deg, ${settings.primary_color} 0%, ${settings.accent_color} 100%)`
        }"
        elevation="8"
        rounded="xl"
      >
        <v-card-text class="pa-12 text-center">
          <h2 class="text-h3 font-weight-bold text-white mb-4">
            Need Assistance?
          </h2>
          <p class="text-h6 text-white mb-8" style="opacity: 0.95;">
            Our team is here to help you with land administration, cadastral mapping, and more.
          </p>
          <v-btn
            href="/contact"
            size="x-large"
            :color="settings.secondary_color"
            elevation="8"
            class="text-none px-12"
          >
            <v-icon start>mdi-email</v-icon>
            Get in Touch
          </v-btn>
        </v-card-text>
      </v-card>
    </v-container>
  </PublicLayout>
</template>

<style scoped>
/* Carousel overlay */
.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
}

/* Fix hover text color for buttons and cards */
:deep(.v-btn) {
  color: inherit !important;
}

:deep(.v-btn--variant-elevated),
:deep(.v-btn--variant-flat) {
  color: white !important;
}

:deep(.v-btn--variant-outlined) {
  color: currentColor !important;
}

:deep(.v-card:hover) {
  color: inherit !important;
}

:deep(.v-card:hover .v-card-title),
:deep(.v-card:hover .v-card-text),
:deep(.v-card:hover .v-card-subtitle) {
  color: inherit !important;
}

/* About content styling */
.about-content {
  line-height: 1.8;
}

.about-content :deep(p) {
  margin-bottom: 1em;
}

.about-content :deep(h1),
.about-content :deep(h2),
.about-content :deep(h3),
.about-content :deep(h4),
.about-content :deep(h5),
.about-content :deep(h6) {
  margin-top: 1.5em;
  margin-bottom: 0.75em;
  font-weight: 600;
  line-height: 1.3;
}

.about-content :deep(h1) {
  font-size: 2em;
}

.about-content :deep(h2) {
  font-size: 1.75em;
}

.about-content :deep(h3) {
  font-size: 1.5em;
}

.about-content :deep(ul),
.about-content :deep(ol) {
  margin-bottom: 1em;
  padding-left: 2em;
}

.about-content :deep(li) {
  margin-bottom: 0.5em;
}

.about-content :deep(strong) {
  font-weight: 600;
}

.about-content :deep(em) {
  font-style: italic;
}

.about-content :deep(a) {
  color: var(--color-primary);
  text-decoration: underline;
}

.about-content :deep(a:hover) {
  opacity: 0.8;
}

.about-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  margin: 1em 0;
}
</style>
