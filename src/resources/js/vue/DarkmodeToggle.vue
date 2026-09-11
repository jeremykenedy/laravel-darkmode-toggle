<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
    defaultMode: { type: String, default: 'system' },
    storageKey: { type: String, default: 'theme' },
    persistUrl: { type: String, default: '' },
    persistMethod: { type: String, default: 'PUT' },
    persistField: { type: String, default: 'dark_mode' },
    className: { type: String, default: 'dark' },
    dataAttribute: { type: String, default: '' },
    colorScheme: { type: Boolean, default: false },
    syncAcrossTabs: { type: Boolean, default: false },
    toggleLabel: { type: String, default: 'Toggle theme' },
    labels: {
        type: Object,
        default: () => ({ light: 'Light', dark: 'Dark', system: 'System' }),
    },
})

const modes = ['light', 'dark', 'system']
const open = ref(false)
const current = ref(props.defaultMode)
const root = ref(null)
const trigger = ref(null)
const menu = ref(null)

let query = null

function read() {
    try {
        const value = localStorage.getItem(props.storageKey)

        return modes.includes(value) ? value : null
    } catch (error) {
        return null
    }
}

function apply(mode) {
    const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)

    document.documentElement.classList.toggle(props.className, isDark)

    if (props.dataAttribute) {
        document.documentElement.setAttribute(props.dataAttribute, isDark ? 'dark' : 'light')
    }

    if (props.colorScheme) {
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light'
    }
}

function setTheme(mode) {
    current.value = mode

    try {
        localStorage.setItem(props.storageKey, mode)
    } catch (error) {
        // Storage can be unavailable in private windows, the theme still applies.
    }

    apply(mode)
    close()

    if (props.persistUrl) {
        fetch(props.persistUrl, {
            method: props.persistMethod,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ [props.persistField]: mode }),
        }).catch(() => {})
    }
}

function close() {
    open.value = false
    trigger.value?.focus()
}

function move(step) {
    open.value = true

    nextTick(() => {
        const items = Array.from(menu.value?.querySelectorAll('[role=menuitemradio]') ?? [])
        const from = items.indexOf(document.activeElement)
        const next = from === -1 ? (step > 0 ? 0 : items.length - 1) : (from + step + items.length) % items.length
        items[next]?.focus()
    })
}

function onSystemChange() {
    if (current.value === 'system') {
        apply('system')
    }
}

function onStorage(event) {
    if (event.key === props.storageKey && modes.includes(event.newValue)) {
        current.value = event.newValue
        apply(event.newValue)
    }
}

function onDocumentClick(event) {
    if (root.value && !root.value.contains(event.target)) {
        open.value = false
    }
}

onMounted(() => {
    current.value = read() || props.defaultMode
    apply(current.value)

    query = window.matchMedia('(prefers-color-scheme: dark)')
    query.addEventListener('change', onSystemChange)
    document.addEventListener('click', onDocumentClick)

    if (props.syncAcrossTabs) {
        window.addEventListener('storage', onStorage)
    }
})

onBeforeUnmount(() => {
    query?.removeEventListener('change', onSystemChange)
    document.removeEventListener('click', onDocumentClick)
    window.removeEventListener('storage', onStorage)
})
</script>

<template>
    <div
        ref="root"
        class="relative"
        @keydown.escape.stop="close"
        @keydown.arrow-down.prevent="move(1)"
        @keydown.arrow-up.prevent="move(-1)"
    >
        <button
            ref="trigger"
            type="button"
            :aria-expanded="open"
            aria-haspopup="true"
            :aria-label="toggleLabel"
            :title="toggleLabel"
            @click="open = !open"
        >
            <svg v-if="current === 'light'" class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            <svg v-else-if="current === 'dark'" class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            <svg v-else class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        </button>

        <div v-if="open" ref="menu" role="menu" class="absolute right-0 mt-1 w-36 rounded-lg bg-white dark:bg-gray-800 shadow-lg z-50">
            <button
                v-for="mode in modes"
                :key="mode"
                type="button"
                role="menuitemradio"
                :aria-checked="current === mode"
                class="block w-full px-4 py-2 text-sm text-left"
                :class="current === mode ? 'font-bold' : ''"
                @click="setTheme(mode)"
            >
                {{ labels[mode] }}
            </button>
        </div>
    </div>
</template>
