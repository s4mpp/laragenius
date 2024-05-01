<?php
namespace S4mpp\Laragenius\Commands;


use Illuminate\Console\Command;
use S4mpp\Laragenius\Generators\Seeder;

class MakeSeederCommand extends Command
{
	protected $signature = 'make:lg-seeder {table}';

	protected $description = 'Generate a new seeder';

	public function handle()
    {
		$table = $this->argument('table');

		$generator = new Seeder($table);

		$filename = $generator->create();

		$this->info('File ['.$filename.'] created.');

    }
}