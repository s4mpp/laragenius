<?php

namespace S4mpp\Laragenius\Commands;

use Illuminate\Console\Command;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Generators\Seeder;

/**
 * @codeCoverageIgnore
 */
class MakeSeederCommand extends Command
{
    protected $signature = 'make:lg-seeder {table}';

    protected $description = 'Generate a new seeder';

    public function handle(): void
    {
        $generator = new Seeder(new Table($this->argument('table')));

        $filename = $generator->create();

        $this->info('File ['.$filename.'] created.');
    }
}
