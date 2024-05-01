<?php
namespace S4mpp\Laragenius\Commands;

use workbench;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\confirm;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\FileManipulation;
use S4mpp\Laragenius\Generators\Model;
use function Laravel\Prompts\multiselect;
use function Orchestra\Testbench\workbench_path;

class MakeModelCommand extends Command
{
	protected $signature = 'make:lg-model {table}';

	protected $description = 'Generate a new model';

	public function handle()
    {
		$table = $this->argument('table');

		$generator = new Model($table);

		$filename = $generator->create();

		$this->info('File ['.$filename.'] created.');

		// dump($model_name);

		// dump(Schema::getIndexes('users'));
		// dump(Schema::getForeignKeys('users'));

        // $fields = Schema::getColumns('users');

		// foreach($fields as $field)
		// {
		// 	dump($field);

		// 	die();
		// }
    }
}