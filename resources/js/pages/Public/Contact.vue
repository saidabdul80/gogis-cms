<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import PublicLayout from '@/layouts/PublicLayout.vue'
import { useTheme } from '@/composables/useTheme'

interface Props {
  settings: {
    site_name: string
    primary_color: string
    secondary_color: string
    accent_color: string
    contact_email?: string
    contact_phone?: string
    address?: string
  }
}

defineProps<Props>()
const { applyTheme } = useTheme()

const form = useForm({
  name: '',
  email: '',
  subject: '',
  message: '',
})

const submit = () => {
  form.post('/contact', {
    onSuccess: () => {
      form.reset()
    },
  })
}

onMounted(() => {
  applyTheme()
})
</script>

<template>
  <PublicLayout title="Contact Us">
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
                Contact Us
              </h1>
              <p class="text-h6 text-white" style="opacity: 0.9;">
                Get in touch with us. We'd love to hear from you!
              </p>
            </v-col>
          </v-row>
        </v-container>
      </v-sheet>
    </v-container>

    <!-- Contact Content -->
    <v-container class="py-12">
      <v-row>
        <!-- Contact Information -->
        <v-col cols="12" md="4">
          <v-card elevation="2" class="pa-6 h-100">
            <v-card-title class="text-h5 mb-4" :style="{ color: settings.primary_color }">
              Contact Information
            </v-card-title>
            
            <v-list>
              <v-list-item v-if="settings.address" prepend-icon="mdi-map-marker">
                <v-list-item-title>Address</v-list-item-title>
                <v-list-item-subtitle>{{ settings.address }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item v-if="settings.contact_phone" prepend-icon="mdi-phone">
                <v-list-item-title>Phone</v-list-item-title>
                <v-list-item-subtitle>{{ settings.contact_phone }}</v-list-item-subtitle>
              </v-list-item>
              
              <v-list-item v-if="settings.contact_email" prepend-icon="mdi-email">
                <v-list-item-title>Email</v-list-item-title>
                <v-list-item-subtitle>{{ settings.contact_email }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>

        <!-- Contact Form -->
        <v-col cols="12" md="8">
          <v-card elevation="2" class="pa-6">
            <v-card-title class="text-h5 mb-4" :style="{ color: settings.primary_color }">
              Send us a Message
            </v-card-title>
            
            <v-form @submit.prevent="submit">
              <v-text-field
                v-model="form.name"
                label="Your Name"
                :error-messages="form.errors.name"
                required
                class="mb-4"
              />
              
              <v-text-field
                v-model="form.email"
                label="Your Email"
                type="email"
                :error-messages="form.errors.email"
                required
                class="mb-4"
              />
              
              <v-text-field
                v-model="form.subject"
                label="Subject"
                :error-messages="form.errors.subject"
                required
                class="mb-4"
              />
              
              <v-textarea
                v-model="form.message"
                label="Message"
                :error-messages="form.errors.message"
                rows="6"
                required
                class="mb-4"
              />
              
              <v-btn
                type="submit"
                :color="settings.primary_color"
                size="large"
                :loading="form.processing"
                class="text-none"
              >
                <v-icon start>mdi-send</v-icon>
                Send Message
              </v-btn>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </PublicLayout>
</template>

