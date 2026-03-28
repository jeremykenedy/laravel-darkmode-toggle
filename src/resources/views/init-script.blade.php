{{-- Dark mode init script - prevents flash of wrong theme. Include in <head>. --}}
<style>[x-cloak] { display: none !important; }</style>
<script>
(function() {
    var key = '{{ config('darkmode.storage_key', 'theme') }}';
    var cls = '{{ config('darkmode.class_name', 'dark') }}';
    var t = localStorage.getItem(key);
    if (t === 'dark' || (t !== 'light' && t !== 'dark' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add(cls);
    }
})();
</script>
