<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Contracts\FakerInterface;

final class Factory extends Generator
{
    protected string $folder = 'database/factories';

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
        $stub = new Stub('stubs/factory/factory');

        $stub->fill([
            'DEFINITION' => $this->getDefinition(),
        ]);

        return $stub;
    }

    private function getDefinition(): string
    {
        $definition = '';

        foreach ($this->getColumns() as $column) {
            $definition .= (new Stub('stubs/factory/definition'))->fill([
                'FIELD_NAME' => $column->getName(),
                'FAKER_DEFINITION' => $this->getFakerDefinition($column),
                'UNIQUE' => $this->getUnique($column),
            ]);
        }

        return trim($definition);
    }

    private function getUnique(Column $column): ?string
    {
        if ($column->isUnique()) {
            return new Stub('stubs/factory/fakers/unique');
        }

        return null;
    }

    private function getFakerDefinition(Column $column): string
    {
        /** @var FakerInterface */
        $field_class = $column->getType()?->class();

        if ($field_class) {
            return (new $field_class())->getFaker($column->getName());
        }

        return 'null';
    }

    /**
     * @return array<Column>
     */
    private function getColumns(): array
    {
        $table = $this->getTable();

        $table->loadColumns()->loadUniqueIndexes()->loadRelationships();

        return array_filter($table->getColumns(), fn ($column) => ! (! empty($column->getRelationships())));
    }
}