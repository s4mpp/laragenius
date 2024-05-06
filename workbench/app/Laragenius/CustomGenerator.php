<?php

namespace Workbench\App\Laragenius;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Generators\Generator;
use S4mpp\Laragenius\Contracts\FakerInterface;

final class CustomGenerator extends Generator
{
    public function getNamespace(): string
    {
        return 'App\CustomGenerator';
    }

    public function getFilename(): string
    {
        return $this->getTable()->getModelName().'Generator';
    }

    public function getContent(): Stub
    {
        $stub = new Stub('workbench/stubs/generator');

        return $stub;
    }
}
