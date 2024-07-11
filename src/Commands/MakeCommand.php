<?php

namespace S4mpp\Laragenius\Commands;

use Illuminate\Console\Command;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Schema\Table;

use function Laravel\Prompts\multiselect;

use S4mpp\Laragenius\Generators\Generator;

/**
 * @codeCoverageIgnore
 */
class MakeCommand extends Command
{
    protected $signature = 'lg:make {table} {--force}';

    protected $description = 'Make new files';

    public function handle(): int
    {
        $table = $this->argument('table');

        Laragenius::forceOverwrite($this->option('force') == true);

        try {
            if (! is_string($table)) {
                throw new \Exception('Table must be a string');
            }

            $table_instance = new Table($table);

            $resources = $this->selectResources();

            foreach ($resources as $resource) {
                /** @var Generator */
                $generator = new $resource($table_instance);

                $filename = $generator->create();

                $this->info('File ['.$filename.'] created.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            dump($e->getMessage());

            return 1;
        }
    }

    /**
     * @return array<int|string>
     */
    private function selectResources(): array
    {
        return multiselect(
            label: 'Select the resources', required: true,
            options: Laragenius::getGenerators(),
            validate: function ($selecteds) {
                foreach ($selecteds as $value) {
                    if (! is_subclass_of($value, Generator::class)) {
                        return $value.' is not a generator';
                    }
                }

                return null;
            }
        );
    }
}
