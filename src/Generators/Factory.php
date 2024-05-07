<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Enums\RelationshipType;
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
        $table = $this->getTable();

        $table->loadColumns()->loadUniqueIndexes()->loadRelationships();

        $stub = new Stub('factory/factory');

        $stub->fill([
            'DEFINITION' => $this->getDefinition(),
        ]);

        return $stub;
    }

    private function getDefinition(): string
    {
        $definition = '';

        foreach ($this->getColumns() as $column) {
            $definition .= (new Stub('factory/definition'))->fill([
                'FIELD_NAME' => $column->getName(),
                'FAKER_DEFINITION' => $this->getFakerDefinition($column),
                'UNIQUE' => $this->getUnique($column),
            ]);
        }

        return trim($definition);
    }

    private function getUnique(Column $column): ?Stub
    {
        if ($column->isUnique()) {
            return new Stub('factory/fakers/unique');
        }

        return null;
    }

    private function getFakerDefinition(Column $column): string
    {
        /** @var FakerInterface|null */
        $field_class = $column->getType()?->class();

        if ($field_class) {
            return (new $field_class())->getFaker($column->getName());
        }

        if($column->isNullable())
        {
            return 'null';
        }

        return new Stub('factory/fakers/word');

    }

    /**
     * @return array<Column>
     */
    private function getColumns(): array
    {
        return array_filter($this->getTable()->getColumns(), function($column) {
            $relationships = array_filter($column->getRelationships(), fn($relationship) => $relationship->getType() == RelationshipType::BelongsTo);

            if(!empty($relationships))
            {
                return false;
            }

            return true;

        });
    }
}
