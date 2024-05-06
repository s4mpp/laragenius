<?php

namespace Workbench\App\Providers;

use S4mpp\Laragenius\Laragenius;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Laragenius\CustomGenerator;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Laragenius::addGenerator(CustomGenerator::class);
    }
    
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
