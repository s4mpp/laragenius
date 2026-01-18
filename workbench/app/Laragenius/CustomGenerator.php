<?php

namespace Workbench\App\Laragenius;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Contracts\Generator;

final class CustomGenerator implements Generator
{
    public function __construct(private Table $table)
    {}

    public function getDestinationPath(): string
    {
        return 'app/Generated';
    }

    public function getFilename(): string
    {
        return 'CustomGenerated';
    }

    public function getStubFile(): string
    {
        return __DIR__.'/../../../workbench/stubs/generator.stub';
    }

    public function mountFile(Stub $stub): void
    {
        $stub->setVariable('NAMESPACE', 'App\Generated');

        $stub->setVariable('STUDLY_NAME', 'CustomGenerated');

        $stub->setVariable('TABLE_NAME', $this->table->getName());
        $stub->setVariable('TABLE_STUDLY_NAME', $this->table->getStudlyName());
    }
}
