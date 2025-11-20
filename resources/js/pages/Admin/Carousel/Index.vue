<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { router, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import GChip from '@/components/GChip.vue'

interface Carousel {
    id: number
    title: string
    description: string | null
    image: string
    button_text: string | null
    button_link: string | null
    order: number
    is_active: boolean
    created_at: string
    updated_at: string
}

const props = defineProps<{
    carousels: Carousel[]
    carouselEnabled: boolean
}>()

const carouselEnabled = ref(props.carouselEnabled)

const toggleCarousel = () => {
    router.post(route('admin.carousel.toggle'), {
        enabled: carouselEnabled.value
    }, {
        preserveScroll: true,
    })
}

const deleteSlide = (id: number) => {
    if (confirm('Are you sure you want to delete this carousel slide?')) {
        router.delete(route('admin.carousel.destroy', id))
    }
}

const toggleActive = (carousel: Carousel) => {
    router.put(route('admin.carousel.update', carousel.id), {
        title: carousel.title,
        description: carousel.description,
        button_text: carousel.button_text,
        button_link: carousel.button_link,
        order: carousel.order,
        is_active: !carousel.is_active,
    }, {
        preserveScroll: true,
        preserveState: true,
    })
}
</script>

<template>
    <AdminLayout>
        <v-container fluid>
            <v-row>
                <v-col cols="12">
                    <div class="d-flex justify-space-between align-center mb-6">
                        <div>
                            <h1 class="text-h4 font-weight-bold">Carousel Management</h1>
                            <p class="text-body-2 text-medium-emphasis mt-1">
                                Manage homepage carousel slides
                            </p>
                        </div>
                        <Link :href="route('admin.carousel.create')">
                            <v-btn
                                color="primary"
                                prepend-icon="mdi-plus"
                            >
                                Add New Slide
                            </v-btn>
                        </Link>
                    </div>
                </v-col>
            </v-row>

            <!-- Carousel Enable/Disable Toggle -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <div class="d-flex justify-space-between align-center">
                                <div>
                                    <h3 class="text-h6 mb-1">Carousel Status</h3>
                                    <p class="text-body-2 text-medium-emphasis">
                                        {{ carouselEnabled ? 'Carousel is currently enabled on homepage' : 'Carousel is currently disabled. Default hero section will be shown.' }}
                                    </p>
                                </div>
                                <v-switch
                                    v-model="carouselEnabled"
                                    color="primary"
                                    hide-details
                                    @change="toggleCarousel"
                                />
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Carousel Slides List -->
            <v-row class="mt-4">
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <span class="text-h6">Carousel Slides</span>
                            <GChip class="ml-2" size="small">{{ carousels.length }} slides</GChip>
                        </v-card-title>
                        <v-divider />
                        
                        <v-card-text v-if="carousels.length === 0" class="text-center py-8">
                            <v-icon size="64" color="grey-lighten-1">mdi-view-carousel</v-icon>
                            <p class="text-h6 mt-4 text-medium-emphasis">No carousel slides yet</p>
                            <p class="text-body-2 text-medium-emphasis">Create your first slide to get started</p>
                            <Link :href="route('admin.carousel.create')">
                                <v-btn
                                    color="primary"
                                    class="mt-4"
                                >
                                    Create First Slide
                                </v-btn>
                            </Link>
                        </v-card-text>

                        <v-list v-else>
                            <v-list-item
                                v-for="carousel in carousels"
                                :key="carousel.id"
                                class="carousel-item"
                            >
                                <template v-slot:prepend>
                                    <v-avatar size="80" rounded="lg">
                                        <v-img :src="carousel.image" cover />
                                    </v-avatar>
                                </template>

                                <v-list-item-title class="text-h6 mb-1">
                                    {{ carousel.title }}
                                    <GChip
                                        :color="carousel.is_active ? 'success' : 'grey'"
                                        size="small"
                                        class="ml-2"
                                    >
                                        {{ carousel.is_active ? 'Active' : 'Inactive' }}
                                    </GChip>
                                </v-list-item-title>
                                <v-list-item-subtitle v-if="carousel.description">
                                    {{ carousel.description }}
                                </v-list-item-subtitle>
                                <v-list-item-subtitle class="mt-1">
                                    <GChip size="x-small" class="mr-2">Order: {{ carousel.order }}</GChip>
                                    <span v-if="carousel.button_text" class="text-caption">
                                        Button: {{ carousel.button_text }}
                                    </span>
                                </v-list-item-subtitle>

                                <template v-slot:append>
                                    <div class="d-flex gap-2">
                                        <v-tooltip text="Toggle Active Status">
                                            <template v-slot:activator="{ props }">
                                                <v-btn
                                                    v-bind="props"
                                                    :icon="carousel.is_active ? 'mdi-eye-off' : 'mdi-eye'"
                                                    size="small"
                                                    variant="text"
                                                    @click="toggleActive(carousel)"
                                                />
                                            </template>
                                        </v-tooltip>

                                        <v-tooltip text="Edit">
                                            <template v-slot:activator="{ props: tooltipProps }">
                                                <Link :href="route('admin.carousel.edit', carousel.id)">
                                                    <v-btn
                                                        v-bind="tooltipProps"
                                                        icon="mdi-pencil"
                                                        size="small"
                                                        variant="text"
                                                    />
                                                </Link>
                                            </template>
                                        </v-tooltip>

                                        <v-tooltip text="Delete">
                                            <template v-slot:activator="{ props }">
                                                <v-btn
                                                    v-bind="props"
                                                    icon="mdi-delete"
                                                    size="small"
                                                    variant="text"
                                                    color="error"
                                                    @click="deleteSlide(carousel.id)"
                                                />
                                            </template>
                                        </v-tooltip>
                                    </div>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AdminLayout>
</template>

<style scoped>
.carousel-item {
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.carousel-item:last-child {
    border-bottom: none;
}
</style>

