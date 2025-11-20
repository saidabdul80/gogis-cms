import { ref } from 'vue'

export interface Toast {
    message: string
    type: 'success' | 'error' | 'warning' | 'info'
    timeout?: number
}

const toasts = ref<Toast[]>([])

export function useToast() {
    const show = (message: string, type: Toast['type'] = 'info', timeout = 5000) => {
        toasts.value.push({ message, type, timeout })
    }

    const success = (message: string, timeout = 5000) => {
        show(message, 'success', timeout)
    }

    const error = (message: string, timeout = 5000) => {
        show(message, 'error', timeout)
    }

    const warning = (message: string, timeout = 5000) => {
        show(message, 'warning', timeout)
    }

    const info = (message: string, timeout = 5000) => {
        show(message, 'info', timeout)
    }

    const remove = (index: number) => {
        toasts.value.splice(index, 1)
    }

    return {
        toasts,
        show,
        success,
        error,
        warning,
        info,
        remove,
    }
}

