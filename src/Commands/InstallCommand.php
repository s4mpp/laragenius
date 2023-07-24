<?php
namespace Samuelpacheco\Laragenius\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;


class InstallCommand extends Command
{
	protected $signature = 'lg:install';

	protected $description = 'Install a Laragenius folder on root of project';

	public function handle(): void
    {
        $folder_name = 'laragenius';

		if(!File::exists($folder_name))
		{
            File::makeDirectory($folder_name);
            
			$this->info("Folder '{$folder_name}' created successfully.");
        }
		else
		{
            $this->error("Folder '{$folder_name}' already exists");
        }
    }
}