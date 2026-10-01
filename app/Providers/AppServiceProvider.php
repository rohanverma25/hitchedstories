<?php

namespace App\Providers;

use App\Support\HomeContent;
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
        // Admin-editable settings and page content, read once per request and
        // shared with the public views.
        View::composer(['layouts.site', 'home', 'blog.show'], function ($view) {
            $attributes = request()->attributes;

            if (! $attributes->has('site')) {
                $attributes->set('site', HomeContent::site());
            }

            $view->with('site', $attributes->get('site'));
        });
    }
}
