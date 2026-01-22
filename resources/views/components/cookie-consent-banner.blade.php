<div
    id="cookie-consent-banner"
    class="fixed inset-x-4 bottom-4 z-50 hidden rounded-2xl border border-zinc-200 bg-white/95 p-6 shadow-2xl backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/95 lg:inset-x-auto lg:right-8 lg:max-w-lg"
>
    <div class="flex flex-col gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500 dark:text-zinc-400">
                {{ __('Cookie Preferences') }}
            </p>
            <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ config('app.name') }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-300">
                {{ __('We use optional cookies to understand traffic and improve your experience. Adjust your preferences below.') }}
            </p>
        </div>

        <div class="space-y-4">
            <label class="flex items-start gap-3 text-sm text-zinc-700 dark:text-zinc-200">
                <input type="checkbox" data-pref="analytics" class="mt-1 h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900" />
                <span>
                    <span class="block font-semibold">{{ __('Allow analytics cookies') }}</span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('These cookies help us measure engagement and improve new features.') }}
                    </span>
                </span>
            </label>

            <div class="space-y-2">
                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('Theme preference') }}</p>
                <div class="flex flex-wrap gap-2" data-theme-selector>
                    @foreach (['system' => __('System'), 'light' => __('Light'), 'dark' => __('Dark')] as $theme => $label)
                        <button
                            type="button"
                            data-theme-option="{{ $theme }}"
                            class="rounded-full border border-zinc-200 px-3 py-1 text-sm font-medium text-zinc-700 transition hover:border-zinc-900 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-200"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
            <button
                type="button"
                data-action="accept-all"
                class="rounded-full border border-zinc-200 px-4 py-2 text-sm font-semibold text-zinc-700 transition hover:border-zinc-900 hover:text-zinc-900 dark:border-zinc-700 dark:text-white"
            >
                {{ __('Accept all') }}
            </button>
            <button
                type="button"
                data-action="save"
                class="rounded-full bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-800"
            >
                {{ __('Save selection') }}
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCookieBanner);
        } else {
            initCookieBanner();
        }

        function initCookieBanner() {
            const helper = window.CookiePreferences;
            const banner = document.getElementById('cookie-consent-banner');

            if (!helper || !banner) {
                return;
            }

            const analyticsToggle = banner.querySelector('[data-pref="analytics"]');
            const themeButtons = banner.querySelectorAll('[data-theme-option]');
            const acceptAllButton = banner.querySelector('[data-action="accept-all"]');
            const saveButton = banner.querySelector('[data-action="save"]');

            let preferences = helper.read();

            function highlightThemeButton() {
                const activeTheme = preferences.theme || 'system';

                themeButtons.forEach((button) => {
                    const isActive = button.dataset.themeOption === activeTheme;
                    button.classList.toggle('bg-zinc-900', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('border-zinc-900', isActive);
                    button.classList.toggle('dark:border-white', isActive);
                    button.classList.toggle('dark:bg-white', isActive && preferences.theme === 'light');
                });
            }

            function syncForm() {
                analyticsToggle.checked = !!preferences.analytics;
                highlightThemeButton();
            }

            function storePreferences(updated) {
                preferences = helper.write(updated);
                helper.applyTheme(preferences.theme || 'system');
            }

            function closeBanner() {
                banner.classList.add('hidden');
            }

            themeButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    preferences.theme = button.dataset.themeOption;
                    helper.applyTheme(preferences.theme);
                    helper.write(preferences);
                    highlightThemeButton();
                });
            });

            acceptAllButton.addEventListener('click', () => {
                storePreferences({
                    consent: true,
                    analytics: true,
                    theme: preferences.theme || 'system',
                });

                closeBanner();
            });

            saveButton.addEventListener('click', () => {
                storePreferences({
                    consent: true,
                    analytics: analyticsToggle.checked,
                    theme: preferences.theme || 'system',
                });

                closeBanner();
            });

            if (!preferences.consent) {
                banner.classList.remove('hidden');
            } else {
                syncForm();
            }
        }
    })();
</script>
