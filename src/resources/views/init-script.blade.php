{{-- Dark mode init script. Runs before paint to prevent a flash of the wrong theme. --}}
@php
    $darkmode = \Jeremykenedy\LaravelDarkmodeToggle\Support\DarkMode::class;
    $storageKey = $darkmode::storageKey();
    $className = $darkmode::className();
    $dataAttribute = $darkmode::dataAttribute();
    $colorScheme = (bool) config('darkmode.color_scheme', false);
    $defaultMode = $darkmode::defaultMode();
@endphp
{{-- Include in <head> with @include('darkmode::init-script'). --}}
<style>[x-cloak]{display:none !important}</style>
<script>
(function () {
    var key = @json($storageKey);
    var cls = @json($className);
    var attr = @json($dataAttribute);
    var fallback = @json($defaultMode);
    var colorScheme = @json($colorScheme);
    var root = document.documentElement;

    function stored() {
        try {
            return window.localStorage.getItem(key);
        } catch (e) {
            return null;
        }
    }

    function prefersDark() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function apply(mode) {
        var isDark = mode === 'dark' || (mode !== 'light' && prefersDark());

        root.classList.toggle(cls, isDark);

        if (attr) {
            root.setAttribute(attr, isDark ? 'dark' : 'light');
        }

        if (colorScheme) {
            root.style.colorScheme = isDark ? 'dark' : 'light';
        }
    }

    var mode = stored() || fallback;

    apply(mode);

    // Keep "system" honest if the OS flips before Alpine, Vue or React boots.
    if (mode !== 'dark' && mode !== 'light' && window.matchMedia) {
        var query = window.matchMedia('(prefers-color-scheme: dark)');
        var onChange = function () {
            if ((stored() || fallback) === 'system') {
                apply('system');
            }
        };

        if (query.addEventListener) {
            query.addEventListener('change', onChange);
        } else if (query.addListener) {
            query.addListener(onChange);
        }
    }
})();
</script>
