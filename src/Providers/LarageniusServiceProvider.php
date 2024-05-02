<?php

namespace S4mpp\Laragenius\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use S4mpp\Laragenius\Commands\MakeModelCommand;
use S4mpp\Laragenius\Commands\MakeSeederCommand;
use S4mpp\Laragenius\Commands\MakeFactoryCommand;

/**
 * @codeCoverageIgnore
 */
class LarageniusServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //TODO create one command, where ask the resource to generate
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModelCommand::class,
                MakeFactoryCommand::class,
                MakeSeederCommand::class,
            ]);
        }
    }
}
