<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;

final class Model extends Generator
{
    protected string $folder = 'app/Models';

    public function getNamespace(): string
    {
        return 'App\Models';
    }

    public function getFilename(): string
    {
        return $this->studly_name;
    }

    public function getContent(): Stub
    {
        $stub = new Stub('model');

        return $stub;
    }
}
