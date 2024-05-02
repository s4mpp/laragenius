<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Contracts\FakerInterface;

final class Factory extends Generator
{
    protected string $folder = 'database/factories';

    protected string $stub_file = 'factory';

    public function getNamespace(): string
    {
        return 'Database\Factories';
    }

    public function getFilename(): string
    {
        return $this->getTable()->getModelName().'Factory';
    }

    public function getContent(): Stub
    {
        $stub = new Stub('factory/factory');

        $stub->fill([
            'DEFINITION' => $this->getDefinition(),
        ]);

        return $stub;
    }

    private function getDefinition(): string
    {
        $definition = '';

        foreach ($this->getColumns() as $column) { dump($column);
            $field_name = $column->getName();

            /** @var FakerInterface */
            $field_class = $column->getType()->class();

            $definition .= (new Stub('factory/definition'))->fill([
                'FIELD_NAME' => $field_name,
                'FAKER_DEFINITION' => (new $field_class())->getFaker($field_name),
                'UNIQUE' => ($column->isUnique()) ? new Stub('factory/fakers/unique') : '',
            ]);
        }

        return $definition;
    }

    /**
     * @return array<Column>
     */
    private function getColumns(): array
    {
        $table = $this->getTable();

        $table->loadUniqueColumns();

        $table->loadColumns();

        return $this->getTable()->getColumns();
    }
}
