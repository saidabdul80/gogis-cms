<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <v-row>
            <v-col cols="12">
                <h1 class="text-h4 mb-6">Welcome back, {{ $page.props.auth.user?.first_name }}!</h1>
            </v-col>
        </v-row>

        <!-- Stats Cards -->
        <v-row>
            <v-col cols="12" md="3">
                <v-card :color="$page.props.appSettings.primaryColor">
                    <v-card-text>
                        <div class="text-white">
                            <v-icon size="40" class="mb-2">mdi-account-group</v-icon>
                            <div class="text-h4 font-weight-bold">0</div>
                            <div class="text-body-1">Total Taxpayers</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="3">
                <v-card :color="$page.props.appSettings.secondaryColor">
                    <v-card-text>
                        <div style="color: #000;">
                            <v-icon size="40" class="mb-2">mdi-file-document</v-icon>
                            <div class="text-h4 font-weight-bold">0</div>
                            <div class="text-body-1">Total Invoices</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="3">
                <v-card :color="$page.props.appSettings.accentColor">
                    <v-card-text>
                        <div class="text-white">
                            <v-icon size="40" class="mb-2">mdi-cash</v-icon>
                            <div class="text-h4 font-weight-bold">₦0</div>
                            <div class="text-body-1">Total Revenue</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="3">
                <v-card color="success">
                    <v-card-text>
                        <div class="text-white">
                            <v-icon size="40" class="mb-2">mdi-check-circle</v-icon>
                            <div class="text-h4 font-weight-bold">0</div>
                            <div class="text-body-1">Paid Invoices</div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </AdminLayout>
</template>

