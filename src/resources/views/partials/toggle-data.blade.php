{{-- Shared Alpine behaviour for the Blade toggle. Only the markup differs per CSS framework. --}}
x-data="{
    open: false,
    current: '{{ $userPreference() }}',
    init() {
        const stored = this.read();
        if (stored) this.current = stored;
        this.apply();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.current === 'system') this.apply();
        });
        @if($syncAcrossTabs)
        window.addEventListener('storage', (event) => {
            if (event.key === '{{ $storageKey }}' && event.newValue) {
                this.current = event.newValue;
                this.apply();
            }
        });
        @endif
    },
    read() {
        try {
            return localStorage.getItem('{{ $storageKey }}');
        } catch (error) {
            return null;
        }
    },
    set(mode) {
        this.current = mode;
        try {
            localStorage.setItem('{{ $storageKey }}', mode);
        } catch (error) {}
        this.apply();
        this.close();
        @if($persistToServer)
        @auth
        fetch('{{ $persistRoute }}', {
            method: '{{ $persistMethod }}',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ {{ $persistField }}: mode }),
        }).catch(() => {});
        @endauth
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
    },
    apply() {
        const isDark = this.current === 'dark' || (this.current === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('{{ $className }}', isDark);
        @if($dataAttribute)
        document.documentElement.setAttribute('{{ $dataAttribute }}', isDark ? 'dark' : 'light');
        @endif
        @if($colorScheme)
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        @endif
    }
}"
x-id="['darkmode-menu']"
@keydown.escape.stop="close()"
@keydown.arrow-down.prevent="open = true; $nextTick(() => move(1))"
@keydown.arrow-up.prevent="open = true; $nextTick(() => move(-1))"
