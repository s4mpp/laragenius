<?php

namespace S4mpp\Laragenius\Commands;

use S4mpp\Laragenius\Stub;
use Illuminate\Console\Command;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Schema\Table;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\multiselect;

use S4mpp\Laragenius\Contracts\Generator;

class MakeCommand extends Command
{
    protected $signature = 'lg:make {table_name} {--force}';

    protected $description = 'Make new files from table';

    public function handle(): int
    {
        $table_name = $this->argument('table_name');

        $force_overwrite = $this->option('force');

        try {
            if (! is_string($table_name)) {
                throw new \Exception('Table must be a string');
            }

            if (! Schema::hasTable($table_name)) {
                throw new \Exception('Tabela ['.$table_name.'] não encontrada');
            }

            $table_instance = new Table($table_name);

            $generators = $this->selectGenerators($table_instance);

            $filesystem = new Filesystem;

            foreach ($generators as $generator) {

                /** @var Generator $instance */
                $instance = new $generator($table_instance);

                $file_path = implode('/', array_filter([Laragenius::getBasePath(), $instance->getDestinationPath(), $instance->getFilename().'.php']));

                if (! $force_overwrite && $filesystem->exists($file_path)) {
                    throw new \Exception('Arquivo ['.$file_path.'] já existe');
                }

                $stub = new Stub($instance->getStubFile());

                $instance->mountFile($stub);

                $stub->fill();

                $filesystem->ensureDirectoryExists(Laragenius::getBasePath().'/'.$instance->getDestinationPath());

                $saved = $filesystem->put($file_path, $stub->getContent());

                if (! $saved) {
                    throw new \Exception('Falha ao criar o arquivo.');
                }

                $this->info('Arquivo ['.$file_path.'] criado.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }
    }

    /**
     * @return array<int|string>
     */
    private function selectGenerators(Table $table_instance): array
    {
        return multiselect(
            label: 'Selecione os geradores',
            required: true,
            options: Laragenius::getGenerators(),
            validate: function ($generators) use ($table_instance): ?string {
                foreach ($generators as $generator) {

                    $instance = new $generator($table_instance);

                    if (! $instance instanceof Generator) {
                        return $generator.' não é um gerador válido. O gerador deve implementar a interface Generator';
                    }
                }

                return null;
            }
        );
    }
}
