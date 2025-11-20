<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

const { appSettings } = useTheme()
const drawer = ref(false)

// Access Ziggy route helper safely
const getRoute = (name: string) => {
  const routeFn = (window as any).route
  if (typeof routeFn === 'function') {
    return routeFn(name)
  }
  // Fallback to simple URLs if Ziggy is not loaded yet
  const routes: Record<string, string> = {
    'home': '/',
    'about': '/about',
    'news.index': '/news',
    'events.index': '/events',
    'contact': '/contact',
  }
  return routes[name] || '/'
}

const navigation = computed(() => [
  { name: 'Home', route: 'home', icon: 'mdi-home', href: getRoute('home') },
  { name: 'About Us', route: 'about', icon: 'mdi-information', href: getRoute('about') },
  { name: 'News', route: 'news.index', icon: 'mdi-newspaper', href: getRoute('news.index') },
  { name: 'Events', route: 'events.index', icon: 'mdi-calendar', href: getRoute('events.index') },
  { name: 'Contact', route: 'contact', icon: 'mdi-email', href: getRoute('contact') },
])

const navigateTo = (href: string) => {
  router.visit(href)
  drawer.value = false
}
</script>

<template>
  <div>
    <!-- Top Bar with Contact Info -->
    <v-app-bar
      :style="{ background: `linear-gradient(90deg, ${appSettings.primaryColor} 0%, ${appSettings.accentColor} 100%)` }"
      density="compact"
      flat
      class="top-bar"
    >
      <v-container fluid>
        <v-row align="center" justify="space-between" no-gutters>
          <v-col cols="auto">
            <div class="d-flex align-center ga-4">
              <v-btn
                v-if="appSettings.contactEmail"
                :href="`mailto:${appSettings.contactEmail}`"
                variant="text"
                size="small"
                color="white"
                class="text-none"
              >
                <v-icon start>mdi-email</v-icon>
                <span class="d-none d-sm-inline">{{ appSettings.contactEmail }}</span>
              </v-btn>
              <v-btn
                v-if="appSettings.contactPhone"
                :href="`tel:${appSettings.contactPhone}`"
                variant="text"
                size="small"
                color="white"
                class="text-none"
              >
                <v-icon start>mdi-phone</v-icon>
                <span class="d-none d-sm-inline">{{ appSettings.contactPhone }}</span>
              </v-btn>
            </div>
          </v-col>
          <v-col cols="auto">
            <div class="d-flex align-center ga-2">
              <v-btn
                v-if="appSettings.fbLink"
                :href="appSettings.fbLink"
                target="_blank"
                icon
                size="small"
                variant="text"
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
                variant="text"
                color="white"
              >
                <v-icon>mdi-instagram</v-icon>
              </v-btn>
              <v-btn
                v-if="appSettings.twitterLink"
                :href="appSettings.twitterLink"
                target="_blank"
                icon
                size="small"
                variant="text"
                color="white"
              >
                <v-icon>mdi-twitter</v-icon>
              </v-btn>
              <v-btn
                v-if="appSettings.linkedinLink"
                :href="appSettings.linkedinLink"
                target="_blank"
                icon
                size="small"
                variant="text"
                color="white"
              >
                <v-icon>mdi-linkedin</v-icon>
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </v-app-bar>

    <!-- Main Navigation -->
    <v-app-bar
      color="white"
      elevation="2"
      height="80"
    >
      <v-container fluid>
        <v-row align="center" no-gutters>
          <!-- Logo -->
          <v-col cols="auto">
            <Link href="/" class="d-flex align-center ga-3 text-decoration-none">
              <v-avatar
                size="56"
                class="elevation-4"
                :style="{ background: `linear-gradient(135deg, ${appSettings.primaryColor} 0%, ${appSettings.accentColor} 100%)` }"
              >
                <v-img
                  v-if="appSettings.logo"
                  :src="appSettings.logo"
                  :alt="appSettings.siteName"
                  cover
                />
                <span v-else class="text-h5 font-weight-bold text-white">
                  {{ appSettings.siteName.charAt(0) }}
                </span>
              </v-avatar>
              <div class="d-none d-md-block">
                <div class="text-h6 font-weight-bold" :style="{ color: appSettings.primaryColor }">{{ appSettings.siteName }}</div>
                <div class="text-caption text-grey-darken-1">{{ appSettings.siteTagline }}</div>
              </div>
            </Link>
          </v-col>

          <v-spacer />

          <!-- Desktop Navigation -->
          <v-col cols="auto" class="d-none d-lg-block">
            <Link
              v-for="item in navigation"
              :key="item.name"
              :href="item.href"
              class="text-decoration-none"
            >
              <v-btn
                variant="text"
                class="text-none mx-1"
                :color="appSettings.primaryColor"
              >
                <v-icon start>{{ item.icon }}</v-icon>
                {{ item.name }}
              </v-btn>
            </Link>

            <!-- Login Button -->
            <Link href="/login" class="text-decoration-none">
              <v-btn
                variant="flat"
                :color="appSettings.primaryColor"
                class="text-none ml-2"
              >
                <v-icon start>mdi-login</v-icon>
                Login
              </v-btn>
            </Link>
          </v-col>

          <!-- Mobile Menu Button -->
          <v-col cols="auto" class="d-lg-none">
            <v-btn
              icon
              @click="drawer = !drawer"
            >
              <v-icon>mdi-menu</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-app-bar>

    <!-- Mobile Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      location="right"
      temporary
    >
      <v-list>
        <v-list-item
          :prepend-avatar="appSettings.logo"
          :title="appSettings.siteName"
          :subtitle="appSettings.siteTagline"
        />
      </v-list>

      <v-divider />

      <v-list density="compact" nav>
        <v-list-item
          v-for="item in navigation"
          :key="item.name"
          :prepend-icon="item.icon"
          :title="item.name"
          @click="navigateTo(item.href)"
        />

        <v-divider class="my-2" />

        <v-list-item
          prepend-icon="mdi-login"
          title="Login"
          @click="navigateTo('/login')"
        />
      </v-list>
    </v-navigation-drawer>
  </div>
</template>



