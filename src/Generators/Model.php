<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Schema\Table;

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

        $table->loadRelationships();

        $stub = new Stub('model/model');

        $stub->fill([
            'RELATIONSHIPS' => $this->getRelationships(),
        ]);

        return $stub;
    }

    private function getRelationships(): string
    {
        $relationships = '';

        $table = $this->getTable();

        $table->loadColumns()->loadRelationships();

        foreach($table->getColumns() as $column)
        {
            foreach ($column->getRelationships() as $relationship) {
                $table_name = $relationship->getTableName();

                $model_name = Table::toModelName($table_name);
    
                $type = $relationship->getType();

                $this->addUse('App\Models\\'.$model_name);
                $this->addUse($type->classRelationLaravel());                
    
                $stub_relationship = new Stub('model/'.$type->stub());
    
                $stub_relationship->fill([
                    'NAME' => $type->nameMethod($table_name),
                    'MODEL' => $model_name,
                ]);
    
                $relationships .= $stub_relationship;
            }
        }

        return trim($relationships);
    }
}
