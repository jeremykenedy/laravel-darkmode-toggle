{{-- Dark mode init script. Runs before paint to prevent a flash of the wrong theme. --}}
{{-- Include in <head>, either with @darkmodeInit or @include('darkmode::init-script'). --}}
<style>[x-cloak]{display:none !important}</style>
<script>
(function () {
    var key = @json(config('darkmode.storage_key', 'theme'));
    var cls = @json(config('darkmode.class_name', 'dark'));
    var attr = @json(config('darkmode.data_attribute'));
    var fallback = @json(config('darkmode.default', 'system'));
    var colorScheme = @json((bool) config('darkmode.color_scheme', false));
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
