<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Utils;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Contracts\Generator;
use S4mpp\Laragenius\Enums\RelationshipType;

final class FactoryGenerator implements Generator
{
    public function __construct(private Table $table)
    {
    }

    /** @var array<string> */
    private array $uses = ['Illuminate\Database\Eloquent\Factories\Factory'];

    public function getDestinationPath(): string
    {
        return 'database/factories';
    }

    public function getFilename(): string
    {
        return $this->table->getStudlyName().'Factory';
    }

    public function getStubFile(): string
    {
        return __DIR__.'/../../stubs/factory/factory.stub';
    }

    public function mountFile(Stub $stub): void
    {
        $stub->setVariable('NAMESPACE', 'Database\Factories');

        $stub->setVariable('MODEL_NAME', $this->table->getStudlyName());

        $stub->setVariable('DEFINITION', $this->getDefinition());

        $stub->setVariable('USES', Utils::mountUses($this->uses));
    }

    private function getDefinition(): string
    {
        $definition = [];

        foreach ($this->table->getColumns() as $column) {

            if (in_array($column->getName(), ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $stub = (new Stub(__DIR__.'/../../stubs/factory/definition.stub'));

            $stub->setVariable('FIELD_NAME', $column->getName());
            $stub->setVariable('FAKER_DEFINITION', $this->getFakerDefinition($column));
            $stub->setVariable('OPTIONAL', $this->getOptionalFlag($column));

            $definition[] = $stub->fill()->getContent();
        }

        return trim(implode('', $definition));
    }

    private function getUniqueFlag(Column $column): ?string
    {
        if (! $column->isUnique()) {
            return null;
        }

        $stub = (new Stub(__DIR__.'/../../stubs/factory/unique.stub'));

        return $stub->getContent();

    }

    private function getOptionalFlag(Column $column): ?string
    {
        if (! $column->isNullable()) {
            return null;
        }

        $stub = (new Stub(__DIR__.'/../../stubs/factory/optional.stub'));

        return $stub->getContent();
    }

    private function getFakerDefinition(Column $column): string
    {
        if ($column->getRelationships()) {
            $belongs_to_relationships = array_filter($column->getRelationships(), fn ($relationship): bool => $relationship->getType() == RelationshipType::BelongsTo);

            if ($belongs_to_relationships !== []) {
                $model_factory = $belongs_to_relationships[0]->getTable()->getStudlyName();

                $this->uses[] = "App\Models\\".$model_factory;

                $stub = new Stub(__DIR__.'/../../stubs/factory/model_factory.stub');

                $stub->setVariable('MODEL', $model_factory);

                return $stub->fill()->getContent();
            }
        }

        $stub = new Stub(__DIR__.'/../../stubs/factory/faker.stub');

        $stub->setVariable('FIELD_NAME', $column->getName());
        $stub->setVariable('UNIQUE', $this->getUniqueFlag($column));
        $stub->setVariable('FAKER', $column->getType()->faker());

        return $stub->fill()->getContent();
    }
}
