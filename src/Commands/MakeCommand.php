<?php

namespace S4mpp\Laragenius\Commands;

use Illuminate\Console\Command;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Generators\Model;
use S4mpp\Laragenius\Generators\Seeder;
use S4mpp\Laragenius\Generators\Factory;

use function Laravel\Prompts\multiselect;

use S4mpp\Laragenius\Generators\Generator;

/**
 * @codeCoverageIgnore
 */
class MakeCommand extends Command
{
    protected $signature = 'lg:make {table}';

    protected $description = 'Make new files';

    public function handle(): void
    {
        $table = $this->argument('table');

        $resources = multiselect(
            label: 'Select the resources you want to generate',
            options: array_merge([
                Model::class,
                Factory::class,
                Seeder::class,
            ], Laragenius::getGenerators()),
            required: true,
            validate: function ($value) {
                if (is_subclass_of($value, Generator::class)) {
                    return $value.' is not a generator';
                }

                return null;
            }
        );

        foreach ($resources as $resource) {
            $generator = new $resource(new Table($table));

            $filename = $generator->create();

            $this->info('File ['.$filename.'] created.');
        }
    }
}
