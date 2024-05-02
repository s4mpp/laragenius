<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Schema\Table;

final class Model extends Generator
{
    protected string $folder = 'app/Models';

    /**
     * @var array<string>
     */
    private array $uses = [];

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
            'USES' => $this->getUses(),
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
    
                $type = $relationship->getType();
    
                $this->uses[] = 'App\Models\\'.Table::toModelName($table_name);
                $this->uses[] = $type->classRelationLaravel();
    
                $stub_relationship = new Stub('model/'.$type->stub());
    
                $stub_relationship->fill([
                    'NAME' => $type->nameMethod($table_name),
                    'MODEL' => Table::toModelName($table_name),
                ]);
    
                $relationships .= $stub_relationship;
            }
        }

        return $relationships;
    }

    private function getUses(): string
    {
        $uses = '';

        foreach (array_unique($this->uses) as $use) {
            $uses .= (new Stub('use'))->fill(['CLASS_PATH' => $use]);
        }

        return $uses;
    }
}
