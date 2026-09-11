<script>
    import { onDestroy, onMount, tick } from 'svelte'

    export let defaultMode = 'system'
    export let storageKey = 'theme'
    export let persistUrl = ''
    export let persistMethod = 'PUT'
    export let persistField = 'dark_mode'
    export let className = 'dark'
    export let dataAttribute = ''
    export let colorScheme = false
    export let toggleLabel = 'Toggle theme'
    export let labels = { light: 'Light', dark: 'Dark', system: 'System' }

    const modes = ['light', 'dark', 'system']

    let open = false
    let current = defaultMode
    let query = null
    let trigger = null
    let menu = null

    function read() {
        try {
            return localStorage.getItem(storageKey)
        } catch (error) {
            return null
        }
    }

    function apply(mode) {
        const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)

        document.documentElement.classList.toggle(className, isDark)

        if (dataAttribute) {
            document.documentElement.setAttribute(dataAttribute, isDark ? 'dark' : 'light')
        }

        if (colorScheme) {
            document.documentElement.style.colorScheme = isDark ? 'dark' : 'light'
        }
    }

    function setTheme(mode) {
        current = mode

        try {
            localStorage.setItem(storageKey, mode)
        } catch (error) {
            // Storage can be unavailable in private windows, the theme still applies.
        }

        apply(mode)
        close()

        if (persistUrl) {
            fetch(persistUrl, {
                method: persistMethod,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ [persistField]: mode }),
            }).catch(() => {})
        }
    }

    function close() {
        open = false
        trigger?.focus()
    }

    async function move(step) {
        open = true

        await tick()

        const items = Array.from(menu?.querySelectorAll('[role=menuitemradio]') ?? [])
        const from = items.indexOf(document.activeElement)
        const next = from === -1 ? (step > 0 ? 0 : items.length - 1) : (from + step + items.length) % items.length
        items[next]?.focus()
    }

    function onKeyDown(event) {
        if (event.key === 'Escape') {
            close()
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault()
            move(1)
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault()
            move(-1)
        }
    }

    function onSystemChange() {
        if (current === 'system') {
            apply('system')
        }
    }

    onMount(() => {
        current = read() || defaultMode
        apply(current)

        query = window.matchMedia('(prefers-color-scheme: dark)')
        query.addEventListener('change', onSystemChange)
    })

    onDestroy(() => {
        query?.removeEventListener('change', onSystemChange)
    })

    function clickOutside(node) {
        const handler = event => {
            if (!node.contains(event.target)) {
                open = false
            }
        }

        document.addEventListener('click', handler, true)

        return {
            destroy() {
                document.removeEventListener('click', handler, true)
            },
        }
    }
</script>

<div class="relative" use:clickOutside on:keydown={onKeyDown}>
    <button
        bind:this={trigger}
        type="button"
        aria-expanded={open}
        aria-haspopup="true"
        aria-label={toggleLabel}
        title={toggleLabel}
        on:click={() => open = !open}
    >
        {#if current === 'light'}
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        {:else if current === 'dark'}
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        {:else}
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        {/if}
    </button>

    {#if open}
        <div bind:this={menu} role="menu" class="absolute right-0 mt-1 w-36 rounded-lg bg-white dark:bg-gray-800 shadow-lg z-50">
            {#each modes as mode}
                <button
                    type="button"
                    role="menuitemradio"
                    aria-checked={current === mode}
                    class="block w-full px-4 py-2 text-sm text-left {current === mode ? 'font-bold' : ''}"
                    on:click={() => setTheme(mode)}
                >{labels[mode]}</button>
            {/each}
        </div>
    {/if}
</div>
