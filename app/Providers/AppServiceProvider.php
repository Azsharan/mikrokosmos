<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->ensureViewCachePathIsWritable();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    protected function ensureViewCachePathIsWritable(): void
    {
        $defaultPath = storage_path('framework/views');

        if (! is_dir($defaultPath)) {
            @mkdir($defaultPath, 0755, true);
        }

        if (is_dir($defaultPath) && is_writable($defaultPath)) {
            return;
        }

        $fallbackPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'mikrokosmos-views';

        if (! is_dir($fallbackPath)) {
            @mkdir($fallbackPath, 0777, true);
        }

        if (is_dir($fallbackPath) && is_writable($fallbackPath)) {
            config(['view.compiled' => $fallbackPath]);
        }
    }
}
