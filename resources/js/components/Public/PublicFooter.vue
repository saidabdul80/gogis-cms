<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

const { appSettings } = useTheme()

const currentYear = new Date().getFullYear()
</script>

<template>
  <v-footer
    :style="{ background: `linear-gradient(135deg, ${appSettings.primaryColor} 0%, ${appSettings.accentColor} 100%)` }"
    class="text-white"
  >
    <v-container>
      <v-row>
        <!-- About Section -->
        <v-col cols="12" md="4">
          <div class="d-flex align-center ga-3 mb-4">
            <v-avatar
              size="48"
              color="white"
            >
              <v-img
                v-if="appSettings.logo"
                :src="appSettings.logo"
                :alt="appSettings.siteName"
              />
              <span v-else class="text-h6 font-weight-bold" :style="{ color: appSettings.primaryColor }">
                {{ appSettings.siteName.charAt(0) }}
              </span>
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-bold">{{ appSettings.siteName }}</div>
              <div class="text-caption">{{ appSettings.siteTagline }}</div>
            </div>
          </div>
          <p class="text-body-2 mb-4">
            {{ appSettings.siteName }} is the agency responsible for digitalized land administration, 
            cadastral mapping, and land registration in Gombe State, Nigeria.
          </p>
        </v-col>

        <!-- Quick Links -->
        <v-col cols="12" md="4">
          <h3 class="text-h6 font-weight-bold mb-4">Quick Links</h3>
          <v-list bg-color="transparent" class="pa-0">
            <v-list-item
              :href="'/'"
              class="px-0 text-white"
              density="compact"
            >
              <v-icon start size="small">mdi-chevron-right</v-icon>
              Home
            </v-list-item>
            <v-list-item
              :href="'/about'"
              class="px-0 text-white"
              density="compact"
            >
              <v-icon start size="small">mdi-chevron-right</v-icon>
              About Us
            </v-list-item>
            <v-list-item
              :href="'/news'"
              class="px-0 text-white"
              density="compact"
            >
              <v-icon start size="small">mdi-chevron-right</v-icon>
              News
            </v-list-item>
            <v-list-item
              :href="'/events'"
              class="px-0 text-white"
              density="compact"
            >
              <v-icon start size="small">mdi-chevron-right</v-icon>
              Events
            </v-list-item>
            <v-list-item
              :href="'/contact'"
              class="px-0 text-white"
              density="compact"
            >
              <v-icon start size="small">mdi-chevron-right</v-icon>
              Contact
            </v-list-item>
          </v-list>
        </v-col>

        <!-- Contact Info -->
        <v-col cols="12" md="4">
          <h3 class="text-h6 font-weight-bold mb-4">Contact Us</h3>
          <v-list bg-color="transparent" class="pa-0">
            <v-list-item
              v-if="appSettings.address"
              class="px-0 text-white"
              density="compact"
            >
              <template #prepend>
                <v-icon>mdi-map-marker</v-icon>
              </template>
              {{ appSettings.address }}
            </v-list-item>
            <v-list-item
              v-if="appSettings.contactEmail"
              :href="`mailto:${appSettings.contactEmail}`"
              class="px-0 text-white"
              density="compact"
            >
              <template #prepend>
                <v-icon>mdi-email</v-icon>
              </template>
              {{ appSettings.contactEmail }}
            </v-list-item>
            <v-list-item
              v-if="appSettings.contactPhone"
              :href="`tel:${appSettings.contactPhone}`"
              class="px-0 text-white"
              density="compact"
            >
              <template #prepend>
                <v-icon>mdi-phone</v-icon>
              </template>
              {{ appSettings.contactPhone }}
            </v-list-item>
          </v-list>

          <!-- Social Media -->
          <div class="d-flex ga-2 mt-4">
            <v-btn
              v-if="appSettings.fbLink"
              :href="appSettings.fbLink"
              target="_blank"
              icon
              size="small"
              variant="outlined"
              color="white"
            >
              <v-icon>mdi-facebook</v-icon>
            </v-btn>
            <v-btn
              v-if="appSettings.instagramLink"
              :href="appSettings.instagramLink"
              target="_blank"
              icon
              size="small"
              variant="outlined"
              color="white"
            >
              <v-icon>mdi-instagram</v-icon>
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <v-divider class="my-4 border-opacity-25" />

      <v-row>
        <v-col cols="12" class="text-center">
          <p class="text-body-2 mb-0">
            {{ appSettings.footerText || `© ${currentYear} ${appSettings.siteName}. All rights reserved.` }}
          </p>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

