<?php

namespace S4mpp\Laragenius\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use S4mpp\Laragenius\Commands\InstallCommand;
use S4mpp\Laragenius\Commands\NewResourceCommand;
use S4mpp\Laragenius\Commands\CreateResourceCommand;
  
class LarageniusServiceProvider extends ServiceProvider 
{
    public function boot()
    {
		if($this->app->runningInConsole())
		{
			$this->commands([
				InstallCommand::class,
				NewResourceCommand::class,
				CreateResourceCommand::class,
			]);
		}
    }
}