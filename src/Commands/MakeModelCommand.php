<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;
use S4mpp\Laragenius\FileManipulation;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;

class MakeModelCommand extends Command
{
	protected $signature = 'make:lg-model';

	protected $description = 'Generate a new model';

	public function handle()
    {
        
    }
}