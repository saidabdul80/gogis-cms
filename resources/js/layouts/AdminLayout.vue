<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

const page = usePage()
const { applyTheme } = useTheme()

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
        title: 'Taxpayers',
        icon: 'mdi-account-group',
        route: 'admin.taxpayers.index',
        value: 'taxpayers'
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

const currentRoute = computed(() => {
    const routeName = page.props.ziggy?.route
    if (typeof routeName === 'string') {
        return routeName
    }
    return ''
})

const isActive = (routeName: string) => {
    return currentRoute.value.startsWith(routeName)
}

const logout = () => {
    router.post('/logout')
}

onMounted(() => {
    applyTheme()
})
</script>

<template>
    <v-app>
        <!-- App Bar -->
        <v-app-bar :color="$page.props.appSettings.primaryColor" prominent>
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
            <v-sheet :color="$page.props.appSettings.primaryColor" class="pa-4">
                <div class="d-flex align-center">
                    <v-avatar size="40" :color="$page.props.appSettings.accentColor" class="mr-3">
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
                    <Link v-else :href="route(item.route)" class="text-decoration-none">
                        <v-list-item
                            :prepend-icon="item.icon"
                            :title="item.title"
                            :value="item.value"
                            :active="isActive(item.route)"
                            :color="$page.props.appSettings.primaryColor"
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

/* Active items should have white text on primary color background */
:deep(.nav-menu .nav-item.v-list-item--active) {
    background-color: v-bind('$page.props.appSettings.primaryColor') !important;
    color: white !important;
}

:deep(.nav-menu .nav-item.v-list-item--active:hover) {
    background-color: v-bind('$page.props.appSettings.primaryColor') !important;
    color: white !important;
}

:deep(.nav-menu .nav-item.v-list-item--active .v-list-item-title),
:deep(.nav-menu .nav-item.v-list-item--active .v-icon) {
    color: white !important;
}

:deep(.nav-menu .nav-item.v-list-item--active:hover .v-list-item-title),
:deep(.nav-menu .nav-item.v-list-item--active:hover .v-icon) {
    color: white !important;
}

/* Force all children to inherit */
:deep(.nav-menu .nav-item *) {
    color: inherit !important;
}

:deep(.nav-menu .nav-item:hover *) {
    color: rgba(0, 0, 0, 0.87) !important;
}

:deep(.nav-menu .nav-item.v-list-item--active *) {
    color: white !important;
}

:deep(.nav-menu .nav-item.v-list-item--active:hover *) {
    color: white !important;
}

/* Ensure active item overlay doesn't override background */
:deep(.nav-menu .nav-item.v-list-item--active .v-list-item__overlay) {
    opacity: 0 !important;
}
</style>

