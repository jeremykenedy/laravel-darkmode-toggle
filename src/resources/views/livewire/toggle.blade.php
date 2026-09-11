<div @include('darkmode::partials.livewire-data') class="relative">
    <button
        x-ref="button"
        @click="open = !open"
        type="button"
        :aria-expanded="open"
        :aria-controls="$id('darkmode-menu')"
        aria-haspopup="true"
        aria-label="{{ __('darkmode::darkmode.toggle_theme') }}"
        title="{{ __('darkmode::darkmode.toggle_theme') }}"
        class="inline-flex items-center p-2 rounded-md text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-500"
    >
        @if($current === 'light')
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        @elseif($current === 'dark')
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        @else
            <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        x-transition
        :id="$id('darkmode-menu')"
        role="menu"
        aria-label="{{ __('darkmode::darkmode.select_theme') }}"
        class="absolute right-0 mt-1 w-36 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 z-50"
    >
        <div class="py-1">
            <button wire:click="setTheme('light')" @click="open = false" type="button" role="menuitemradio" aria-checked="{{ $current === 'light' ? 'true' : 'false' }}" class="flex items-center gap-2 w-full px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline-none focus-visible:bg-gray-50 dark:focus-visible:bg-gray-700 {{ $current === 'light' ? 'font-medium text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">{{ __('darkmode::darkmode.light') }}</button>
            <button wire:click="setTheme('dark')" @click="open = false" type="button" role="menuitemradio" aria-checked="{{ $current === 'dark' ? 'true' : 'false' }}" class="flex items-center gap-2 w-full px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline-none focus-visible:bg-gray-50 dark:focus-visible:bg-gray-700 {{ $current === 'dark' ? 'font-medium text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">{{ __('darkmode::darkmode.dark') }}</button>
            <button wire:click="setTheme('system')" @click="open = false" type="button" role="menuitemradio" aria-checked="{{ $current === 'system' ? 'true' : 'false' }}" class="flex items-center gap-2 w-full px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus-visible:outline-none focus-visible:bg-gray-50 dark:focus-visible:bg-gray-700 {{ $current === 'system' ? 'font-medium text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">{{ __('darkmode::darkmode.system') }}</button>
        </div>
    </div>
</div>
