import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useTheme as useVuetifyTheme } from 'vuetify'

export function useTheme() {
  const page = usePage()
  const vuetifyTheme = useVuetifyTheme()

  const appSettings = computed(() => page.props.appSettings as {
    siteName: string
    siteTagline: string
    logo: string
    primaryColor: string
    secondaryColor: string
    accentColor: string
    contactEmail: string
    contactPhone: string
    address: string
    fbLink: string
    instagramLink: string
    twitterLink: string
    linkedinLink: string
    footerText: string
  })

  // Apply theme colors to CSS variables and Vuetify theme
  const applyTheme = () => {
    if (typeof document !== 'undefined' && appSettings.value) {
      // Apply to CSS variables
      const root = document.documentElement
      root.style.setProperty('--color-primary', appSettings.value.primaryColor)
      root.style.setProperty('--color-secondary', appSettings.value.secondaryColor)
      root.style.setProperty('--color-accent', appSettings.value.accentColor)

      // Apply to Vuetify theme dynamically
      vuetifyTheme.themes.value.gogisTheme = {
        dark: false,
        colors: {
          primary: appSettings.value.primaryColor,
          secondary: appSettings.value.secondaryColor,
          accent: appSettings.value.accentColor,
          error: '#ef4444',
          info: '#3b82f6',
          success: '#10b981',
          warning: '#f59e0b',
          background: '#f9fafb',
          surface: '#ffffff',
          'on-background': '#000000',
          'on-surface': '#000000',
          'on-primary': '#ffffff',
          'on-secondary': '#000000',
          'on-success': '#ffffff',
          'on-warning': '#000000',
          'on-error': '#ffffff',
          'on-info': '#ffffff',
        },
        variables: {},
      }
    }
  }

  return {
    appSettings,
    applyTheme,
  }
}

