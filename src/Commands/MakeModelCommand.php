<?php

namespace S4mpp\Laragenius\Commands;

use Illuminate\Console\Command;
use S4mpp\Laragenius\Schema\Table;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Model;

/**
 * @codeCoverageIgnore
 */
class MakeModelCommand extends Command
{
    protected $signature = 'make:lg-model {table}';

    protected $description = 'Generate a new model';

    public function handle(): void
    {
        $generator = new Model(new Table($this->argument('table')));

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
