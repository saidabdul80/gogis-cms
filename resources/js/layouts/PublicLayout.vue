<script setup lang="ts">
import { onMounted, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'
import PublicHeader from '@/components/Public/PublicHeader.vue'
import PublicFooter from '@/components/Public/PublicFooter.vue'

interface Props {
  title?: string
  description?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: '',
  description: '',
})

const { appSettings, applyTheme } = useTheme()

// Apply theme on mount and when settings change
onMounted(() => {
  applyTheme()
})

watch(() => appSettings.value, () => {
  applyTheme()
}, { deep: true })
</script>

<template>
  <v-app>
    <Head>
      <title>{{ title ? `${title} - ${appSettings.siteName}` : appSettings.siteName }}</title>
      <meta name="description" :content="description || appSettings.siteTagline" />
    </Head>

    <PublicHeader />

    <v-main>
      <slot />
    </v-main>

    <PublicFooter />
  </v-app>
</template>

