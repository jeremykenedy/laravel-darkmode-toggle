{{-- Shared Alpine behaviour for the Livewire toggle. The server owns the mode, Alpine only mirrors it onto the document. --}}
x-data="{
    open: false,
    mode: '{{ $current }}',
    init() {
        this.apply(this.mode);
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => this.apply(this.mode));
    },
    apply(mode) {
        this.mode = mode;
        try {
            localStorage.setItem('{{ config('darkmode.storage_key', 'theme') }}', mode);
        } catch (error) {}
        const isDark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('{{ config('darkmode.class_name', 'dark') }}', isDark);
        @if(config('darkmode.data_attribute'))
        document.documentElement.setAttribute('{{ config('darkmode.data_attribute') }}', isDark ? 'dark' : 'light');
        @endif
        @if(config('darkmode.color_scheme', false))
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
        @endif
    }
}"
x-id="['darkmode-menu']"
@theme-changed.window="apply($event.detail.mode)"
@keydown.escape.stop="open = false; $refs.button.focus()"
