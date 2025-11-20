<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useForm, Head, Link } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

interface Props {
    status?: string
    canResetPassword?: boolean
    settings: {
        site_name: string
        logo?: string
        primary_color: string
        secondary_color: string
        accent_color: string
    }
}

const props = defineProps<Props>()
const { applyTheme } = useTheme()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

const submit = () => {
    form.post('/login', {
        onFinish: () => {
            form.password = ''
        },
    })
}

onMounted(() => {
    applyTheme()
})
</script>

<template>
    <Head title="Admin Login" />

    <v-app>
        <v-main>
            <v-container fluid class="fill-height pa-0">
                <v-row no-gutters class="fill-height">
                    <!-- Left Side - Branding -->
                    <v-col cols="12" md="6" class="d-none d-md-flex">
                        <v-sheet
                            :style="{
                                background: `linear-gradient(135deg, ${settings.primary_color} 0%, ${settings.accent_color} 100%)`,
                            }"
                            class="fill-height d-flex flex-column align-center justify-center pa-12"
                        >
                            <div class="text-center">
                                <v-img
                                    v-if="settings.logo"
                                    :src="settings.logo"
                                    max-width="200"
                                    class="mb-8 mx-auto"
                                />
                                <h1 class="text-h2 font-weight-bold text-white mb-4">
                                    {{ settings.site_name }}
                                </h1>
                                <p class="text-h6 text-white" style="opacity: 0.9;">
                                    Geographic Information System
                                </p>
                                <p class="text-body-1 text-white mt-6" style="opacity: 0.8;">
                                    Secure Admin Portal
                                </p>
                            </div>

                            <!-- Decorative Elements -->
                            <div class="mt-12">
                                <v-icon size="100" color="white" style="opacity: 0.2;">
                                    mdi-shield-lock
                                </v-icon>
                            </div>
                        </v-sheet>
                    </v-col>

                    <!-- Right Side - Login Form -->
                    <v-col cols="12" md="6" class="d-flex align-center justify-center">
                        <v-sheet class="pa-8 pa-md-12" max-width="500" width="100%">
                            <!-- Mobile Logo -->
                            <div class="d-md-none text-center mb-8">
                                <v-img
                                    v-if="settings.logo"
                                    :src="settings.logo"
                                    max-width="120"
                                    class="mx-auto mb-4"
                                />
                                <h2 class="text-h5 font-weight-bold" :style="{ color: settings.primary_color }">
                                    {{ settings.site_name }}
                                </h2>
                            </div>

                            <!-- Login Header -->
                            <div class="mb-8">
                                <h2 class="text-h4 font-weight-bold mb-2">
                                    Admin Login
                                </h2>
                                <p class="text-body-1 text-medium-emphasis">
                                    Enter your credentials to access the admin panel
                                </p>
                            </div>

                            <!-- Status Message -->
                            <v-alert
                                v-if="status"
                                type="success"
                                variant="tonal"
                                class="mb-6"
                            >
                                {{ status }}
                            </v-alert>

                            <!-- Login Form -->
                            <v-form @submit.prevent="submit">
                                <v-text-field
                                    v-model="form.email"
                                    label="Email Address"
                                    type="email"
                                    prepend-inner-icon="mdi-email"
                                    :error-messages="form.errors.email"
                                    :color="settings.primary_color"
                                    variant="outlined"
                                    required
                                    autofocus
                                    class="mb-4"
                                />

                                <v-text-field
                                    v-model="form.password"
                                    label="Password"
                                    :type="showPassword ? 'text' : 'password'"
                                    prepend-inner-icon="mdi-lock"
                                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="showPassword = !showPassword"
                                    :error-messages="form.errors.password"
                                    :color="settings.primary_color"
                                    variant="outlined"
                                    required
                                    class="mb-4"
                                />

                                <v-checkbox
                                    v-model="form.remember"
                                    label="Remember me"
                                    :color="settings.primary_color"
                                    hide-details
                                    class="mb-6"
                                />

                                <v-btn
                                    type="submit"
                                    :color="settings.primary_color"
                                    size="large"
                                    block
                                    :loading="form.processing"
                                    class="text-none mb-4"
                                >
                                    <v-icon start>mdi-login</v-icon>
                                    Sign In
                                </v-btn>

                                <div v-if="canResetPassword" class="text-center">
                                    <Link href="/forgot-password" class="text-decoration-none">
                                        <v-btn
                                            variant="text"
                                            :color="settings.primary_color"
                                            class="text-none"
                                        >
                                            Forgot your password?
                                        </v-btn>
                                    </Link>
                                </div>
                            </v-form>

                            <!-- Back to Home -->
                            <v-divider class="my-6" />

                            <div class="text-center">
                                <Link href="/" class="text-decoration-none">
                                    <v-btn
                                        variant="text"
                                        :color="settings.primary_color"
                                        class="text-none"
                                    >
                                        <v-icon start>mdi-arrow-left</v-icon>
                                        Back to Home
                                    </v-btn>
                                </Link>
                            </div>
                        </v-sheet>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>
