<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Relationship;
use S4mpp\Laragenius\Enums\RelationshipType;

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

        $stub->fill([
            'FOR' => $this->getFors(),
        ]);

        return $stub;
    }

    //TODO add deeply relationship for
    private function getFors(): string
    {
        $for = '';

        foreach ($this->getTable()->getColumns() as $column) {
            foreach ($column->getRelationships() as $relationship) {
                if ($relationship->getType() != RelationshipType::BelongsTo) {
                    continue;
                }

                $for .= $this->getFor($relationship);
            }
        }

        return $for;
    }

    private function getFor(Relationship $relationship): string
    {
        $table_name = $relationship->getTableName();

        $model_name = Table::toModelName($table_name);

        //TODO add correctly namespace model
        $this->addUse('App\Models\\'.$model_name);

        return (new Stub('seeder/for'))->fill([
            'NAME' => $relationship->getType()->nameMethod($table_name),
            'MODEL' => $model_name,
        ]);
    }
}
