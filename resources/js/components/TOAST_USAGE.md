# Toast Notification System

A beautiful, reusable toast notification system that works globally across the application and automatically handles backend flash messages.

## Features

- ✅ **Beautiful Design** - Uses Vuetify's snackbar with custom styling
- ✅ **Multiple Types** - Success, Error, Warning, Info
- ✅ **Auto-stacking** - Multiple toasts stack vertically
- ✅ **Auto-dismiss** - Configurable timeout (default 5 seconds)
- ✅ **Manual Close** - Close button on each toast
- ✅ **Backend Integration** - Automatically shows Laravel flash messages
- ✅ **Global Access** - Available in any component via composable

## Usage

### 1. From Backend (Laravel Controllers)

The toast system automatically picks up flash messages from Laravel:

```php
// Success message
return redirect()->back()->with('success', 'Settings updated successfully!');

// Error message
return redirect()->back()->with('error', 'Something went wrong!');

// Warning message
return redirect()->back()->with('warning', 'Please review your changes.');

// Info message
return redirect()->back()->with('info', 'New feature available!');
```

### 2. From Frontend (Vue Components)

Import and use the `useToast` composable:

```vue
<script setup lang="ts">
import { useToast } from '@/composables/useToast'

const toast = useToast()

// Success toast
const handleSuccess = () => {
    toast.success('Operation completed successfully!')
}

// Error toast
const handleError = () => {
    toast.error('Failed to save changes')
}

// Warning toast
const handleWarning = () => {
    toast.warning('This action cannot be undone')
}

// Info toast
const handleInfo = () => {
    toast.info('New updates available')
}

// Custom timeout (in milliseconds)
const handleCustom = () => {
    toast.success('This will disappear in 10 seconds', 10000)
}
</script>

<template>
    <div>
        <v-btn @click="handleSuccess">Show Success</v-btn>
        <v-btn @click="handleError">Show Error</v-btn>
        <v-btn @click="handleWarning">Show Warning</v-btn>
        <v-btn @click="handleInfo">Show Info</v-btn>
    </div>
</template>
```

### 3. With Inertia Form Submissions

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

const toast = useToast()
const form = useForm({ name: '' })

const submit = () => {
    form.post('/api/endpoint', {
        onSuccess: () => {
            toast.success('Form submitted successfully!')
        },
        onError: () => {
            toast.error('Failed to submit form')
        },
    })
}
</script>
```

## Toast Types

### Success (Green)
- Icon: Check circle
- Color: Green
- Use for: Successful operations, confirmations

### Error (Red)
- Icon: Alert circle
- Color: Red
- Use for: Errors, failures, validation issues

### Warning (Orange)
- Icon: Alert
- Color: Orange
- Use for: Warnings, cautions, important notices

### Info (Blue)
- Icon: Information
- Color: Blue
- Use for: General information, tips, updates

## Customization

### Change Default Timeout

```typescript
// 3 seconds instead of 5
toast.success('Quick message', 3000)

// 10 seconds
toast.error('Important error', 10000)
```

### Styling

The toast component uses Vuetify's theming system and can be customized via:
- `resources/js/components/Toast.vue` - Component styling
- `resources/js/composables/useToast.ts` - Toast logic

## Technical Details

- **Location**: Top right corner
- **Stacking**: Vertical with 80px spacing
- **Animation**: Smooth slide-in/out with cubic-bezier easing
- **Z-index**: 9999 (always on top)
- **Max Width**: 500px
- **Min Width**: 344px
- **Auto-dismiss**: Yes (configurable)
- **Manual Close**: Yes (X button)

## Examples in GOGIS

1. **Settings Page** - Shows success toast after updating settings
2. **Form Submissions** - Shows error toast on validation failure
3. **CRUD Operations** - Shows success/error toasts for create/update/delete
4. **File Uploads** - Shows progress and completion toasts

