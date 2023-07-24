<?php

namespace SamuelPacheco\Laragenius;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use Samuelpacheco\Laragenius\Commands\InstallCommand;
  
class LarageniusServiceProvider extends ServiceProvider 
{
    public function boot()
    {
		AboutCommand::add('Laragenius', fn () => ['Version' => '1.0.0']);

		if($this->app->runningInConsole())
		{
			$this->commands([
				InstallCommand::class
			]);
		}
    }
}