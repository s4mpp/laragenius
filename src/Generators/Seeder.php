<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Enums\RelationshipType;

final class Seeder extends Generator
{
    protected string $folder = 'database/seeders';

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
        $stub = new Stub('stubs/seeder/seeder');

        $stub->fill([
            'FOR' => $this->getFor(),
        ]);

        return $stub;
    }

    private function getFor(): string
    {
        $for = '';

        $table = $this->getTable();

        $table->loadColumns()->loadRelationships();

        foreach ($table->getColumns() as $column) {
            foreach ($column->getRelationships() as $relationship) {
                $type = $relationship->getType();

                if ($type != RelationshipType::BelongsTo) {
                    continue;
                }

                $table_name = $relationship->getTableName();

                $model_name = Table::toModelName($table_name);

                $this->addUse('App\Models\\'.$model_name);

                $stub_for = new Stub('stubs/seeder/for');

                $stub_for->fill([
                    'NAME' => $type->nameMethod($table_name),
                    'MODEL' => $model_name,
                ]);

                $for .= $stub_for;
            }
        }

        return $for;
    }
}
