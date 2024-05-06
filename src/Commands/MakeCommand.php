<?php

namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Schema\Table;
use function Laravel\Prompts\search;

use Illuminate\Support\Facades\Schema;
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

    public function handle(): int
    {
        $table = $this->argument('table');
        
        try
        {
            $table_instance = new Table($table);
            
            $resources = $this->selectResources();
            
            foreach ($resources as $resource) {
    
                /** @var Generator */
                $generator = new $resource($table_instance);

                $filename = $generator->create();
    
                $this->info('File ['.$filename.'] created.');
            }

            return 0;

        }
        catch(\Exception $e)
        {
            $this->error($e->getMessage());
            return 1;
        }
    }


    /**
     * @return array<string>
     */
    private function selectResources(): array
    {
        return multiselect(
            label: 'Select the resources', required: true,
            options: Laragenius::getGenerators(),
            validate: function ($selecteds) {

                foreach($selecteds as $value)
                {
                    if (!is_subclass_of($value, Generator::class)) {
                        return $value.' is not a generator';
                    }
                }


                return null;
            }
        );
    }
}
