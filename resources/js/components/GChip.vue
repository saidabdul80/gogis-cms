<template>
    <span 
        :class="chipClasses"
        :style="chipStyles"
    >
        <slot></slot>
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
    color?: 'success' | 'error' | 'warning' | 'info' | 'primary' | 'secondary' | 'grey' | 'default'
    size?: 'x-small' | 'small' | 'default' | 'large' | 'x-large'
    variant?: 'flat' | 'outlined' | 'text'
}

const props = withDefaults(defineProps<Props>(), {
    color: 'default',
    size: 'default',
    variant: 'flat'
})

const chipClasses = computed(() => {
    const classes = ['g-chip']
    
    // Size classes
    if (props.size === 'x-small') classes.push('g-chip--x-small')
    else if (props.size === 'small') classes.push('g-chip--small')
    else if (props.size === 'large') classes.push('g-chip--large')
    else if (props.size === 'x-large') classes.push('g-chip--x-large')
    
    // Variant classes
    if (props.variant === 'outlined') classes.push('g-chip--outlined')
    else if (props.variant === 'text') classes.push('g-chip--text')
    
    return classes
})

const chipStyles = computed(() => {
    const colorMap: Record<string, { bg: string; text: string; border?: string }> = {
        success: { bg: '#4caf50', text: '#ffffff' },
        error: { bg: '#f44336', text: '#ffffff' },
        warning: { bg: '#ff9800', text: '#ffffff' },
        info: { bg: '#2196f3', text: '#ffffff' },
        primary: { bg: 'var(--color-primary)', text: '#ffffff' },
        secondary: { bg: 'var(--color-secondary)', text: '#000000' },
        grey: { bg: '#9e9e9e', text: 'rgba(0, 0, 0, 0.87)' },
        default: { bg: '#e0e0e0', text: 'rgba(0, 0, 0, 0.87)' }
    }
    
    const colors = colorMap[props.color] || colorMap.default
    
    if (props.variant === 'outlined') {
        return {
            backgroundColor: 'transparent',
            color: colors.bg,
            border: `1px solid ${colors.bg}`
        }
    } else if (props.variant === 'text') {
        return {
            backgroundColor: 'transparent',
            color: colors.bg
        }
    }
    
    return {
        backgroundColor: colors.bg,
        color: colors.text
    }
})
</script>

<style scoped>
.g-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 12px;
    height: 32px;
    border-radius: 16px;
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 1;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.g-chip--x-small {
    height: 20px;
    padding: 0 6px;
    font-size: 0.625rem;
    border-radius: 10px;
}

.g-chip--small {
    height: 24px;
    padding: 0 8px;
    font-size: 0.75rem;
    border-radius: 12px;
}

.g-chip--large {
    height: 40px;
    padding: 0 16px;
    font-size: 1rem;
    border-radius: 20px;
}

.g-chip--x-large {
    height: 48px;
    padding: 0 20px;
    font-size: 1.125rem;
    border-radius: 24px;
}

.g-chip--outlined {
    border-width: 1px;
    border-style: solid;
}

.g-chip--text {
    background-color: transparent !important;
}
</style>

