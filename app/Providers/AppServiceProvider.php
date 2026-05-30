<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ADDED: tell Laravel how to resolve SuggestionEngine
        // (it needs KeywordExtractor injected, Laravel handles the rest)
        $this->app->bind(\App\Services\SuggestionEngine::class, function ($app) {
            return new \App\Services\SuggestionEngine(
                $app->make(\App\Services\KeywordExtractor::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
