<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    defaultMode: { type: String, default: 'system' },
    storageKey: { type: String, default: 'theme' },
    persistUrl: { type: String, default: '' },
    persistMethod: { type: String, default: 'PUT' },
    persistField: { type: String, default: 'dark_mode' },
    className: { type: String, default: 'dark' },
})

const open = ref(false)
const current = ref(props.defaultMode)

function apply(mode) {
    const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
    document.documentElement.classList.toggle(props.className, isDark)
}

function setTheme(mode) {
    current.value = mode
    localStorage.setItem(props.storageKey, mode)
    apply(mode)
    open.value = false
    if (props.persistUrl) {
        fetch(props.persistUrl, {
            method: props.persistMethod,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content, 'Accept': 'application/json' },
            body: JSON.stringify({ [props.persistField]: mode }),
        }).catch(() => {})
    }
}

onMounted(() => {
    const stored = localStorage.getItem(props.storageKey)
    if (stored) current.value = stored
    apply(current.value)
})
</script>

<template>
    <div class="relative" v-click-outside="() => open = false">
        <button @click="open = !open" type="button" title="Toggle theme">
            <svg v-if="current === 'light'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg v-else-if="current === 'dark'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        </button>
        <div v-if="open" class="absolute right-0 mt-1 w-36 rounded-lg bg-white dark:bg-gray-800 shadow-lg z-50">
            <button v-for="opt in ['light', 'dark', 'system']" :key="opt" @click="setTheme(opt)" class="block w-full px-4 py-2 text-sm text-left" :class="current === opt ? 'font-bold' : ''">{{ opt.charAt(0).toUpperCase() + opt.slice(1) }}</button>
        </div>
    </div>
</template>
