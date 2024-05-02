<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;

final class Seeder extends Generator
{
    protected string $folder = 'database/seeders';

    public function getNamespace(): string
    {
        return 'Database\Seeders';
    }

    public function getFilename(): string
    {
        return $this->studly_name.'Seeder';
    }

    public function getContent(): Stub
    {
        $stub = new Stub('seeder');

        return $stub;
    }
}
