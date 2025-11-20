<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'
import { useToast } from '@/composables/useToast'
import Toast from '@/components/Toast.vue'

const page = usePage()
const { applyTheme, appSettings } = useTheme()
const { success: showSuccess } = useToast()

// Debug: Log when flash messages change
onMounted(() => {
    console.log('AdminLayout mounted, flash messages:', page.props.flash)
    applyTheme()
})

// Watch for settings changes and reapply theme
watch(() => appSettings.value, () => {
    applyTheme()
}, { deep: true })

const user = computed(() => page.props.auth?.user)
const drawer = ref(true)

const menuItems = [
    {
        title: 'Dashboard',
        icon: 'mdi-view-dashboard',
        route: 'admin.dashboard',
        value: 'dashboard'
    },
    {
        divider: true,
        subheader: 'CONTENT MANAGEMENT'
    },
    {
        title: 'Settings',
        icon: 'mdi-cog',
        route: 'admin.settings.index',
        value: 'settings'
    },
    {
        title: 'Carousel',
        icon: 'mdi-view-carousel',
        route: 'admin.carousel.index',
        value: 'carousel'
    },
    {
        title: 'Pages',
        icon: 'mdi-file-document-multiple',
        route: 'admin.pages.index',
        value: 'pages'
    },
    {
        title: 'News',
        icon: 'mdi-newspaper',
        route: 'admin.news.index',
        value: 'news'
    },
    {
        title: 'Events',
        icon: 'mdi-calendar',
        route: 'admin.events.index',
        value: 'events'
    },
    {
        title: 'Media Gallery',
        icon: 'mdi-image-multiple',
        route: 'admin.media.index',
        value: 'media'
    },
    {
        divider: true,
        subheader: 'REVENUE MANAGEMENT'
    },
    {
        title: 'Individual Customers',
        icon: 'mdi-account',
        route: 'admin.individual-customers.index',
        value: 'individual-customers'
    },
    {
        title: 'Corporate Customers',
        icon: 'mdi-domain',
        route: 'admin.corporate-customers.index',
        value: 'corporate-customers'
    },
    {
        title: 'Property Types',
        icon: 'mdi-shape',
        route: 'admin.property-types.index',
        value: 'property-types'
    },
    {
        title: 'Property Profiling',
        icon: 'mdi-home-city',
        route: 'admin.properties.index',
        value: 'properties'
    },
    {
        title: 'Invoices',
        icon: 'mdi-file-document',
        route: 'admin.invoices.index',
        value: 'invoices'
    },
    {
        title: 'Payments',
        icon: 'mdi-cash',
        route: 'admin.payments.index',
        value: 'payments'
    },
    {
        title: 'Revenue Heads',
        icon: 'mdi-chart-line',
        route: 'admin.revenue-heads.index',
        value: 'revenue-heads'
    },
    {
        divider: true,
        subheader: 'SYSTEM'
    },
    {
        title: 'Admins',
        icon: 'mdi-shield-account',
        route: 'admin.admins.index',
        value: 'admins'
    },
    {
        title: 'Activity Logs',
        icon: 'mdi-history',
        route: 'admin.logs.index',
        value: 'logs'
    }
]

const currentRoute = computed(()  => {
    return page.props.currentRoute
})

const isActive = (routeName: string) => {
    //split by . and first compare with full name the compare with first part only
    return currentRoute.value?.startsWith(routeName)
}

const logout = () => {
    router.post('/logout')
}
</script>

<template>
    <v-app>
        <!-- App Bar -->
        <v-app-bar color="primary" prominent>
            <v-app-bar-nav-icon @click="drawer = !drawer" color="white" />
            
            <v-app-bar-title class="text-white">
                <v-icon icon="mdi-view-dashboard" class="mr-2" />
                {{ $page.props.appSettings.siteName }} - Admin Panel
            </v-app-bar-title>

            <v-spacer />

            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        icon="mdi-account-circle"
                        color="white"
                    />
                </template>
                <v-list>
                    <v-list-item>
                        <v-list-item-title>{{ user?.full_name || user?.email }}</v-list-item-title>
                        <v-list-item-subtitle>{{ user?.email }}</v-list-item-subtitle>
                    </v-list-item>
                    <v-divider />
                    <v-list-item>
                        <v-btn
                            variant="text"
                            prepend-icon="mdi-logout"
                            @click="logout"
                            block
                        >
                            Logout
                        </v-btn>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Navigation Drawer -->
        <v-navigation-drawer v-model="drawer" permanent width="280">
            <!-- Sidebar Header -->
            <v-sheet color="primary" class="pa-4">
                <div class="d-flex align-center">
                    <!-- use logo if not availalble then use mdi-shield-account -->
                     <v-img
                        v-if="$page.props.appSettings.logo"
                        :src="$page.props.appSettings.logo"
                        width="50"
                        class="mr-3"
                    />
                    <v-avatar v-else size="40" color="accent" class="mr-3">
                        <v-icon color="white">mdi-shield-account</v-icon>
                    </v-avatar>
                    <div>
                        <div class="text-subtitle-1 font-weight-bold text-white">
                            Admin Panel
                        </div>
                        <div class="text-caption text-white" style="opacity: 0.8;">
                            {{ $page.props.appSettings.siteName }}
                        </div>
                    </div>
                </div>
            </v-sheet>

            <!-- Navigation Menu -->
            <v-list nav class="py-2 nav-menu">
                <template v-for="(item, index) in menuItems" :key="index">
                    <v-divider v-if="item.divider && !item.subheader" class="my-2" />
                    <v-list-subheader v-else-if="item.subheader">{{ item.subheader }}</v-list-subheader>
                    <Link v-else :href="route(item.route)" class="text-decoration-none" >
                        <v-list-item
                            :prepend-icon="item.icon"
                            :title="item.title"
                            :value="item.value"
                            :active="isActive(item.route)"
                            color="primary"
                            base-color="primary"
                            rounded="lg"
                            class="mx-2 mb-1 nav-item"
                        />
                    </Link>
                </template>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <v-container fluid>
                <slot />
            </v-container>
        </v-main>

        <!-- Toast Notifications -->
        <Toast />
    </v-app>
</template>

<style scoped>
/* Force text color visibility on navigation items */
:deep(.nav-menu .nav-item) {
    color: rgba(0, 0, 0, 0.87) !important;
}

:deep(.nav-menu .nav-item:hover) {
    color: rgba(0, 0, 0, 0.87) !important;
}

:deep(.nav-menu .nav-item .v-list-item-title),
:deep(.nav-menu .nav-item .v-icon) {
    color: rgba(0, 0, 0, 0.87) !important;
}

:deep(.nav-menu .nav-item:hover .v-list-item-title),
:deep(.nav-menu .nav-item:hover .v-icon) {
    color: rgba(0, 0, 0, 0.87) !important;
}

/* Force active items to use primary color background */
:deep(.nav-menu .v-list-item.v-list-item--active) {
    background-color: rgb(var(--v-theme-primary)) !important;
    color: rgb(var(--v-theme-on-primary)) !important;
}

:deep(.nav-menu .v-list-item.v-list-item--active .v-list-item__overlay) {
    opacity: 0 !important;
}

:deep(.nav-menu .v-list-item.v-list-item--active .v-list-item__underlay) {
    opacity: 0 !important;
}

/* Ensure active items have white text */
:deep(.nav-menu .v-list-item.v-list-item--active .v-list-item-title),
:deep(.nav-menu .v-list-item.v-list-item--active .v-list-item__prepend .v-icon) {
    color: rgb(var(--v-theme-on-primary)) !important;
}

:deep(.nav-menu .v-list-item.v-list-item--active:hover) {
    background-color: rgb(var(--v-theme-primary)) !important;
}

:deep(.nav-menu .v-list-item.v-list-item--active:hover .v-list-item-title),
:deep(.nav-menu .v-list-item.v-list-item--active:hover .v-list-item__prepend .v-icon) {
    color: rgb(var(--v-theme-on-primary)) !important;
}
</style>

