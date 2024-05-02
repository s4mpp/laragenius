<?php

namespace S4mpp\Laragenius\Commands;

use Illuminate\Console\Command;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Generators\Factory;

/**
 * @codeCoverageIgnore
 */
class MakeFactoryCommand extends Command
{
    protected $signature = 'make:lg-factory {table}';

    protected $description = 'Generate a new factory';

    public function handle(): void
    {
        $generator = new Factory(new Table($this->argument('table')));

        $filename = $generator->create();

        $this->info('File ['.$filename.'] created.');
    }
}
