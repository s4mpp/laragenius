<?php

namespace S4mpp\Laragenius\Providers;

use Laravel\Prompts\Prompt;
use Illuminate\Support\ServiceProvider;
use S4mpp\Laragenius\Commands\MakeCommand;

/**
 * @codeCoverageIgnore
 */
class LarageniusServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../../stubs/laragenius-config.stub' => config_path('laragenius.php'),
            ], 'laragenius-config');
        }
    }
}
