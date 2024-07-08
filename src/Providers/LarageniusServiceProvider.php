<?php

namespace S4mpp\Laragenius\Providers;

use Illuminate\Support\ServiceProvider;
use S4mpp\Laragenius\Commands\MakeCommand;

/**
 * @codeCoverageIgnore
 */
//TODO criar classe auxiliar para clientes testarem comandos personalizados
class LarageniusServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCommand::class,
            ]);
        }
    }
}
