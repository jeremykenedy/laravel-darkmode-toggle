{{-- Shared Alpine behaviour for the Livewire toggle. The server owns the mode, Alpine only mirrors it onto the document. --}}
@php
    $darkmode = \Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode::class;
    $storageKey = $darkmode::storageKey();
    $className = $darkmode::className();
    $dataAttribute = $darkmode::dataAttribute();
    $colorScheme = (bool) config('darkmode.color_scheme', false);
    $syncAcrossTabs = (bool) config('darkmode.sync_across_tabs', false);
@endphp
x-data="{
    open: false,
    mode: '{{ $current }}',
    init() {
        this.apply(this.mode);
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => this.apply(this.mode));
        @if($syncAcrossTabs)
        window.addEventListener('storage', (event) => {
            if (event.key === '{{ $storageKey }}' && event.newValue) {
                this.apply(event.newValue);
            }
        });
        @endif
    },
    apply(mode) {
        this.mode = mode;
        try {
            localStorage.setItem('{{ $storageKey }}', mode);
        } catch (error) {}
        const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('{{ $className }}', isDark);
        @if($dataAttribute)
        document.documentElement.setAttribute('{{ $dataAttribute }}', isDark ? 'dark' : 'light');
        @endif
        @if($colorScheme)
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        @endif
    },
    close() {
        this.open = false;
        this.$refs.button.focus();
    },
    move(step) {
        const items = Array.from(this.$refs.menu.querySelectorAll('[role=menuitemradio]'));
        const from = items.indexOf(document.activeElement);
        const next = from === -1 ? (step > 0 ? 0 : items.length - 1) : (from + step + items.length) % items.length;
        items[next].focus();
    }
}"
x-id="['darkmode-menu']"
@theme-changed.window="apply($event.detail.mode)"
@keydown.escape.stop="close()"
@keydown.arrow-down.prevent="open = true; $nextTick(() => move(1))"
@keydown.arrow-up.prevent="open = true; $nextTick(() => move(-1))"
