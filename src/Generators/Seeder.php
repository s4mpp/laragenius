<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Utils;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Contracts\Generator;

final class Seeder implements Generator
{
    public function __construct(private Table $table)
    {
    }

    public function getDestinationPath(): string
    {
        return 'database/seeders';
    }

    public function getFilename(): string
    {
        return $this->table->getStudlyName().'Seeder';
    }

    public function getStubFile(): string
    {
        return __DIR__.'/../../stubs/seeder/seeder.stub';
    }

    public function mountFile(Stub $stub): void
    {
        $model_name = $this->table->getStudlyName();

        $stub->setVariable('NAMESPACE', 'Database\Seeders');

        $stub->setVariable('MODEL_NAME', $model_name);

        $uses = [
            "App\Models\\".$model_name,
            'Illuminate\Database\Seeder',
        ];

        $stub->setVariable('USES', Utils::mountUses($uses));
    }
}
