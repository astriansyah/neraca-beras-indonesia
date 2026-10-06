<?php

namespace App\Providers;

use App\Models\DataPoint;
use App\Models\Source;
use App\Observers\DataPointObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Invalidate cache saat DataPoint berubah
        DataPoint::observe(DataPointObserver::class);

        // Daftar sumber data untuk footer setiap halaman.
        View::composer('components.layouts.app', function ($view) {
            $view->with('footerSources', Cache::remember(
                'footer.sources',
                config('dashboard.cache_ttl'),
                fn () => Source::orderBy('publisher')->orderBy('title')->get(['title', 'publisher', 'url'])->toArray()
            ));
        });
    }
}
