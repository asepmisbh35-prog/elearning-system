<?php

namespace App\Providers;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
        try {
            $setting = SchoolSetting::current();
        } catch (\Throwable $e) {
            // Tabel belum ada (sebelum migrate) — jangan sampai app crash
            $setting = null;
        }

        View::share('schoolSetting', $setting);
    }
}
