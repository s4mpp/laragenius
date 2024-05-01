<?php
namespace S4mpp\Laragenius\Commands;

use workbench;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;

use function Laravel\Prompts\note;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\FileManipulation;
use S4mpp\Laragenius\Generators\Factory;

use function Laravel\Prompts\multiselect;
use function Orchestra\Testbench\workbench_path;

class MakeFactoryCommand extends Command
{
	protected $signature = 'make:lg-factory {table}';

	protected $description = 'Generate a new factory';

	public function handle()
    {
		$table = $this->argument('table');

		$generator = new Factory($table);

		$filename = $generator->create();

		$this->info('File ['.$filename.'] created.');
    }
}