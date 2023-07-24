<?php
namespace Samuelpacheco\Laragenius\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
	protected $signature = 'lg:install';

	protected $description = 'Install a Laragenius folder on root of project';

	public function handle(): void
    {
        $this->info('Hello world!');
    }
}