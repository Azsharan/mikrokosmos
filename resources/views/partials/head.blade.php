@php
    static $appIconSource;
    static $appIconInitialized = false;

    if (! $appIconInitialized) {
        $logoPath = storage_path('app/logo-noBG.png');
        $appIconSource = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
        $appIconInitialized = true;
    }
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

@if ($appIconSource)
    <link rel="icon" href="{{ $appIconSource }}" sizes="any" type="image/png">
    <link rel="apple-touch-icon" href="{{ $appIconSource }}">
@else
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
@endif

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<script>
    (function () {
        const STORAGE_KEY = 'mikrokosmos_cookie_preferences';

        function readPreferences() {
            const match = document.cookie.split('; ').find((row) => row.startsWith(STORAGE_KEY + '='));
            if (!match) {
                return { consent: false, analytics: false, theme: 'system' };
            }

            try {
                return Object.assign({ consent: false, analytics: false, theme: 'system' }, JSON.parse(decodeURIComponent(match.split('=')[1])));
            } catch (error) {
                console.warn('Unable to parse cookie preferences', error);

                return { consent: false, analytics: false, theme: 'system' };
            }
        }

        function persistPreferences(preferences) {
            const payload = encodeURIComponent(JSON.stringify(preferences));
            document.cookie = STORAGE_KEY + '=' + payload + '; path=/; max-age=' + 60 * 60 * 24 * 365 + '; SameSite=Lax';

            return preferences;
        }

        function applyTheme(theme) {
            const resolvedTheme = theme || 'system';
            const root = document.documentElement;
            root.dataset.theme = resolvedTheme;

            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = resolvedTheme === 'dark' || (resolvedTheme === 'system' && prefersDark);

            root.classList.toggle('dark', shouldUseDark);
            localStorage.setItem('ui-theme', resolvedTheme);
        }

        function handleSystemThemeChange() {
            const preferences = readPreferences();
            if ((preferences.theme || 'system') === 'system') {
                applyTheme('system');
            }
        }

        const initialPreferences = readPreferences();
        applyTheme(initialPreferences.theme || 'system');

        window.CookiePreferences = {
            read: readPreferences,
            write(preferences) {
                const merged = Object.assign({}, readPreferences(), preferences);
                persistPreferences(merged);
                return merged;
            },
            applyTheme,
        };

        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        if (mediaQuery?.addEventListener) {
            mediaQuery.addEventListener('change', handleSystemThemeChange);
        } else if (mediaQuery?.addListener) {
            mediaQuery.addListener(handleSystemThemeChange);
        }
    })();
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
