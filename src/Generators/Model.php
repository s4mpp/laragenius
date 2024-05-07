<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Relationship;

final class Model extends Generator
{
    protected string $folder = 'app/Models';

    public function getNamespace(): string
    {
        return 'App\Models';
    }

    public function getFilename(): string
    {
        return $this->getTable()->getModelName();
    }

    public function getContent(): Stub
    {        
        $table = $this->getTable();

        $table->loadColumns()->loadRelationships();

        $stub = new Stub('model/model');

        $stub->fill([
            'CASTS' => $this->getCasts(),
            'RELATIONSHIPS' => $this->getRelationships(),
        ]);

        return $stub;
    }

    private function getCasts(): ?Stub
    {
        $casts = $this->getCast();

        if (empty($casts)) {
            return null;
        }

        return (new Stub('model/casts'))->fill([
            'CASTS' => trim($casts),
        ]);
    }

    private function getCast(): string
    {
        $casts = '';

        foreach ($this->getTable()->getColumns() as $column) {
            $cast_type = $column->getType()?->cast();

            if (! $cast_type) {
                continue;
            }

            $casts .= (new Stub('model/cast'))->fill([
                'NAME' => $column->getName(),
                'CASTTYPE' => $cast_type,
            ]);
        }

        return $casts;
    }

    private function getRelationships(): string
    {
        $relationships = '';

        foreach ($this->getTable()->getColumns(filter: false) as $column) {
            foreach ($column->getRelationships() as $relationship) {
                $relationships .= $this->getRelationship($relationship);
            }
        }

        return trim($relationships);
    }

    private function getRelationship(Relationship $relationship): string
    {
        $table_name = $relationship->getTableName();

        $model_name = Table::toModelName($table_name);

        $type = $relationship->getType();

        $this->addUse('App\Models\\'.$model_name);
        $this->addUse($type->classRelationLaravel());

        return (new Stub('model/'.$type->stub()))->fill([
            'NAME' => $type->nameMethod($table_name),
            'MODEL' => $model_name,
        ]);
    }
}
