<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;

final class Seeder extends Generator
{
    protected string $folder = 'database/seeders';

    public function __construct(private Table $table)
    {
        parent::__construct($table);

        $this->addUse("App\Models\\".$table->getModelName());
        $this->addUse('Illuminate\Database\Seeder');
    }

    public function getNamespace(): string
    {
        return 'Database\Seeders';
    }

    public function getFilename(): string
    {
        return $this->getTable()->getModelName().'Seeder';
    }

    public function getContent(): Stub
    {
        $table = $this->getTable();

        $table->loadColumns()->loadRelationships();

        $stub = new Stub('seeder/seeder');

        $stub->fill();

        return $stub;
    }
}
