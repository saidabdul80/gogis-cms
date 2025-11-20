<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref, watch, onMounted } from 'vue'

const props = defineProps<{
    settings: Record<string, any>
}>()

const form = useForm({
    site_name: props.settings.site_name || '',
    site_description: props.settings.site_description || '',
    primary_color: String(props.settings.primary_color || '#1c6434'),
    secondary_color: String(props.settings.secondary_color || '#fecd07'),
    accent_color: String(props.settings.accent_color || '#c39913'),
    contact_email: props.settings.contact_email || '',
    contact_phone: props.settings.contact_phone || '',
    address: props.settings.address || '',
    fb_link: props.settings.fb_link || '',
    twitter_link: props.settings.twitter_link || '',
    instagram_link: props.settings.instagram_link || '',
    linkedin_link: props.settings.linkedin_link || '',
    about_us: props.settings.about_us || '',
    dg_name: props.settings.dg_name || '',
    dg_title: props.settings.dg_title || '',
    dg_bio: props.settings.dg_bio || '',
    logo: null as File | null,
    dg_image: null as File | null,
    // Social Media API Settings
    social_media_auto_post: props.settings.social_media_auto_post === 'true',
    facebook_auto_post: props.settings.facebook_auto_post === 'true',
    facebook_page_id: props.settings.facebook_page_id || '',
    facebook_access_token: props.settings.facebook_access_token || '',
    twitter_auto_post: props.settings.twitter_auto_post === 'true',
    twitter_api_key: props.settings.twitter_api_key || '',
    twitter_api_secret: props.settings.twitter_api_secret || '',
    twitter_access_token: props.settings.twitter_access_token || '',
    twitter_access_token_secret: props.settings.twitter_access_token_secret || '',
    linkedin_auto_post: props.settings.linkedin_auto_post === 'true',
    linkedin_access_token: props.settings.linkedin_access_token || '',
    linkedin_organization_id: props.settings.linkedin_organization_id || '',
})

// Get initial tab from URL hash
const getTabFromHash = () => {
    const hash = window.location.hash.replace('#', '')
    return hash || 'general'
}

const tab = ref(getTabFromHash())

// Update URL hash when tab changes
watch(tab, (newTab) => {
    window.location.hash = newTab
})

// Listen for hash changes (browser back/forward)
onMounted(() => {
    window.addEventListener('hashchange', () => {
        tab.value = getTabFromHash()
    })
})

const submit = () => {
    // Always use POST with _method for file uploads compatibility
    const formData = new FormData()

    // Add all text fields
    Object.keys(form.data()).forEach(key => {
        if (key !== 'logo' && key !== 'dg_image') {
            const value = form[key as keyof typeof form.data]
            if (value !== null && value !== undefined) {
                formData.append(key, String(value))
            }
        }
    })

    // Add files if present
    if (form.logo && form.logo instanceof File) {
        formData.append('logo', form.logo)
    }
    if (form.dg_image && form.dg_image instanceof File) {
        formData.append('dg_image', form.dg_image)
    }

    // Add _method for Laravel method spoofing
    formData.append('_method', 'PUT')

    router.post(route('admin.settings.update'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('logo', 'dg_image')
        },
    })
}
</script>

<template>
    <Head title="Settings" />

    <AdminLayout>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-6">Site Settings</h1>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12">
                <v-card class="pa-4">
                    <v-tabs v-model="tab" :color="$page.props.appSettings.primaryColor">
                        <v-tab class="mr-1" value="general">General</v-tab>
                        <v-tab class="mr-1" value="branding">Theme</v-tab>
                        <v-tab class="mr-1" value="contact">Contact Info</v-tab>
                        <v-tab class="mr-1" value="social">Social Media</v-tab>
                        <v-tab class="mr-1" value="social-api">Social Media API</v-tab>
                        <v-tab class="mr-1" value="content">Content</v-tab>
                    </v-tabs>

                    <v-card-text class="px-0 py-4">
                        <form @submit.prevent="submit" class="mt-2">
                            <v-window v-model="tab" class="pt-2">
                                <!-- General Tab -->
                                <v-window-item value="general">
                                    <v-row class="pa-0">
                                        <v-col cols="12" md="6" >
                                            <v-text-field
                                                v-model="form.site_name"
                                                label="Site Name"
                                                :error-messages="form.errors.site_name"
                                                required
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea
                                                v-model="form.site_description"
                                                label="Site Description"
                                                :error-messages="form.errors.site_description"
                                                rows="3"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Branding Tab -->
                                <v-window-item value="branding">
                                    <v-row>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                v-model="form.primary_color"
                                                label="Primary Color"
                                                type="color"
                                                :error-messages="form.errors.primary_color"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                v-model="form.secondary_color"
                                                label="Secondary Color"
                                                type="color"
                                                :error-messages="form.errors.secondary_color"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                v-model="form.accent_color"
                                                label="Accent Color"
                                                type="color"
                                                :error-messages="form.errors.accent_color"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-file-input
                                                v-model="form.logo"
                                                label="Logo"
                                                accept="image/*"
                                                :error-messages="form.errors.logo"
                                                prepend-icon="mdi-image"
                                            />
                                            <div v-if="settings.logo" class="mt-2">
                                                <img :src="settings.logo" alt="Current Logo" style="max-height: 100px;" />
                                            </div>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-file-input
                                                v-model="form.dg_image"
                                                label="Director General Image"
                                                accept="image/*"
                                                :error-messages="form.errors.dg_image"
                                                prepend-icon="mdi-account"
                                            />
                                            <div v-if="settings.dg_image" class="mt-2">
                                                <img :src="settings.dg_image" alt="DG Image" style="max-height: 100px;" />
                                            </div>
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Contact Info Tab -->
                                <v-window-item value="contact">
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.contact_email"
                                                label="Email"
                                                type="email"
                                                :error-messages="form.errors.contact_email"
                                                prepend-icon="mdi-email"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.contact_phone"
                                                label="Phone"
                                                :error-messages="form.errors.contact_phone"
                                                prepend-icon="mdi-phone"
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea
                                                v-model="form.address"
                                                label="Address"
                                                :error-messages="form.errors.address"
                                                prepend-icon="mdi-map-marker"
                                                rows="3"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Social Media Tab -->
                                <v-window-item value="social">
                                    <v-row>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.fb_link"
                                                label="Facebook URL"
                                                :error-messages="form.errors.fb_link"
                                                prepend-icon="mdi-facebook"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.twitter_link"
                                                label="Twitter URL"
                                                :error-messages="form.errors.twitter_link"
                                                prepend-icon="mdi-twitter"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.instagram_link"
                                                label="Instagram URL"
                                                :error-messages="form.errors.instagram_link"
                                                prepend-icon="mdi-instagram"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.linkedin_link"
                                                label="LinkedIn URL"
                                                :error-messages="form.errors.linkedin_link"
                                                prepend-icon="mdi-linkedin"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Social Media API Tab -->
                                <v-window-item value="social-api">
                                    <v-alert type="info" variant="tonal" class="mb-4">
                                        <v-alert-title>Social Media Auto-Posting</v-alert-title>
                                        Configure API credentials to automatically post news articles to your social media platforms when published.
                                    </v-alert>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-switch
                                                v-model="form.social_media_auto_post"
                                                label="Enable Social Media Auto-Posting"
                                                color="primary"
                                                hide-details
                                                class="mb-4"
                                            />
                                        </v-col>
                                    </v-row>

                                    <v-divider class="my-4" />

                                    <!-- Facebook Settings -->
                                    <v-row>
                                        <v-col cols="12">
                                            <h3 class="text-h6 mb-2">
                                                <v-icon class="mr-2">mdi-facebook</v-icon>
                                                Facebook
                                            </h3>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-switch
                                                v-model="form.facebook_auto_post"
                                                label="Enable Facebook Auto-Post"
                                                color="primary"
                                                hide-details
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.facebook_page_id"
                                                label="Facebook Page ID"
                                                :error-messages="form.errors.facebook_page_id"
                                                prepend-icon="mdi-identifier"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.facebook_access_token"
                                                label="Facebook Access Token"
                                                :error-messages="form.errors.facebook_access_token"
                                                prepend-icon="mdi-key"
                                                type="password"
                                            />
                                        </v-col>
                                    </v-row>

                                    <v-divider class="my-4" />

                                    <!-- Twitter Settings -->
                                    <v-row>
                                        <v-col cols="12">
                                            <h3 class="text-h6 mb-2">
                                                <v-icon class="mr-2">mdi-twitter</v-icon>
                                                Twitter / X
                                            </h3>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-switch
                                                v-model="form.twitter_auto_post"
                                                label="Enable Twitter Auto-Post"
                                                color="primary"
                                                hide-details
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.twitter_api_key"
                                                label="Twitter API Key"
                                                :error-messages="form.errors.twitter_api_key"
                                                prepend-icon="mdi-key"
                                                type="password"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.twitter_api_secret"
                                                label="Twitter API Secret"
                                                :error-messages="form.errors.twitter_api_secret"
                                                prepend-icon="mdi-key"
                                                type="password"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.twitter_access_token"
                                                label="Twitter Access Token"
                                                :error-messages="form.errors.twitter_access_token"
                                                prepend-icon="mdi-key"
                                                type="password"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.twitter_access_token_secret"
                                                label="Twitter Access Token Secret"
                                                :error-messages="form.errors.twitter_access_token_secret"
                                                prepend-icon="mdi-key"
                                                type="password"
                                            />
                                        </v-col>
                                    </v-row>

                                    <v-divider class="my-4" />

                                    <!-- LinkedIn Settings -->
                                    <v-row>
                                        <v-col cols="12">
                                            <h3 class="text-h6 mb-2">
                                                <v-icon class="mr-2">mdi-linkedin</v-icon>
                                                LinkedIn
                                            </h3>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-switch
                                                v-model="form.linkedin_auto_post"
                                                label="Enable LinkedIn Auto-Post"
                                                color="primary"
                                                hide-details
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.linkedin_access_token"
                                                label="LinkedIn Access Token"
                                                :error-messages="form.errors.linkedin_access_token"
                                                prepend-icon="mdi-key"
                                                type="password"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.linkedin_organization_id"
                                                label="LinkedIn Organization ID"
                                                :error-messages="form.errors.linkedin_organization_id"
                                                prepend-icon="mdi-identifier"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-window-item>

                                <!-- Content Tab -->
                                <v-window-item value="content">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-textarea
                                                v-model="form.about_us"
                                                label="About Us"
                                                :error-messages="form.errors.about_us"
                                                rows="5"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.dg_name"
                                                label="Director General Name"
                                                :error-messages="form.errors.dg_name"
                                            />
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.dg_title"
                                                label="Director General Title"
                                                :error-messages="form.errors.dg_title"
                                            />
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea
                                                v-model="form.dg_bio"
                                                label="Director General Bio"
                                                :error-messages="form.errors.dg_bio"
                                                rows="5"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-window-item>
                            </v-window>

                            <v-divider class="my-4" />

                            <v-btn
                                type="submit"
                                :color="$page.props.appSettings.primaryColor"
                                :loading="form.processing"
                                size="large"
                            >
                                <v-icon start>mdi-content-save</v-icon>
                                Save Settings
                            </v-btn>
                        </form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>
