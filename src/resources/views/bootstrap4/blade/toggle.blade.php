<div
    @include(config('darkmode.prefix', 'darkmode').'::partials.toggle-data')
    {{ $attributes->merge(['class' => 'dropdown d-inline-block']) }}
>
    <button
        x-ref="button"
        @click="open = !open"
        type="button"
        :aria-expanded="open"
        :aria-controls="$id('darkmode-menu')"
        aria-haspopup="true"
        aria-label="{{ __('darkmode::darkmode.toggle_theme') }}"
        title="{{ __('darkmode::darkmode.toggle_theme') }}"
        class="btn btn-link text-secondary p-1"
    >
        <svg x-show="current === 'light'" x-cloak width="20" height="20" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        <svg x-show="current === 'dark'" x-cloak width="20" height="20" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        <svg x-show="current === 'system'" x-cloak width="20" height="20" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
    </button>

    <div
        x-ref="menu"
        x-show="open"
        x-cloak
        @click.outside="open = false"
        :id="$id('darkmode-menu')"
        role="menu"
        aria-label="{{ __('darkmode::darkmode.select_theme') }}"
        class="dropdown-menu show"
        style="position:absolute;right:0;"
    >
        <button @click="set('light')" type="button" role="menuitemradio" :aria-checked="current === 'light'" class="dropdown-item" :class="current === 'light' ? 'active' : ''">{{ __('darkmode::darkmode.light') }}</button>
        <button @click="set('dark')" type="button" role="menuitemradio" :aria-checked="current === 'dark'" class="dropdown-item" :class="current === 'dark' ? 'active' : ''">{{ __('darkmode::darkmode.dark') }}</button>
        <button @click="set('system')" type="button" role="menuitemradio" :aria-checked="current === 'system'" class="dropdown-item" :class="current === 'system' ? 'active' : ''">{{ __('darkmode::darkmode.system') }}</button>
    </div>
</div>
