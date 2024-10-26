<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Enums\RelationshipType;
use S4mpp\Laragenius\Contracts\FakerInterface;

final class Factory extends Generator
{
    protected string $folder = 'database/factories';

    public function __construct(private Table $table)
    {
        parent::__construct($table);

        $this->addUse('Illuminate\Database\Eloquent\Factories\Factory');
    }

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

        foreach ($this->getTable()->getColumns() as $column) {
            $definition .= (new Stub('factory/definition'))->fill([
                'FIELD_NAME' => $column->getName(),
                'FAKER_DEFINITION' => $this->getFakerDefinition($column),
                'UNIQUE' => $this->getUnique($column),
                'OPTIONAL' => $this->getOptional($column),
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

    private function getOptional(Column $column): ?Stub
    {
        if ($column->isNullable()) {
            return new Stub('factory/fakers/optional');
        }

        return null;
    }

    private function getFakerDefinition(Column $column): string
    {
        /** @var FakerInterface|null */
        $field_class = $column->getType()?->class();

        if ($field_class) {
            $relationships = array_filter($column->getRelationships(), fn ($relationship) => $relationship->getType() == RelationshipType::BelongsTo);

            if (! empty($relationships)) {
                $model_factory = Table::toModelName($relationships[0]->getTableName());

                $this->addUse("App\Models\\".$model_factory);

                return (new Stub('factory/fakers/factory'))->fill([
                    'MODEL' => $model_factory,
                ]);
            }

            return (new $field_class())->getFaker($column->getName());
        }

        if ($column->isNullable()) {
            return 'null';
        }

        return new Stub('factory/fakers/word');
    }
}
