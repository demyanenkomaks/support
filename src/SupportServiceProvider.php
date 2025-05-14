<?php

namespace Maksde\Support;

use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/support.php', 'support');
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'support');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path('vendor/support'),
            ], 'support-lang');

            $this->publishes([
                __DIR__.'/../config/support.php' => config_path('support.php'),
            ], 'support-config');
        }
    }
}
