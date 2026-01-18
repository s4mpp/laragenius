<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Utils;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Contracts\Generator;

final class Model implements Generator
{
    public function __construct(private Table $table)
    {
    }

    /** @var array<string> */
    private array $uses = [
        'Illuminate\Database\Eloquent\Model',
        'Illuminate\Database\Eloquent\Factories\HasFactory',
    ];

    public function getDestinationPath(): string
    {
        return 'app/Models';
    }

    public function getFilename(): string
    {
        return $this->table->getStudlyName();
    }

    public function getStubFile(): string
    {
        return __DIR__.'/../../stubs/model/model.stub';
    }

    public function mountFile(Stub $stub): void
    {
        $model_name = $this->table->getStudlyName();

        $stub->setVariable('NAMESPACE', 'App\Models');

        $stub->setVariable('MODEL_NAME', $model_name);

        $stub->setVariable('CASTS', $this->getCasts());

        $stub->setVariable('RELATIONSHIPS', $this->getRelationships());

        $uses[] = "Database\Factories\\".$model_name.'Factory';

        $stub->setVariable('USES', Utils::mountUses($this->uses));
    }

    private function getCasts(): ?string
    {
        $casts = [];

        foreach ($this->table->getColumns() as $column) {
            $cast_type = $column->getType()->cast();

            if (! $cast_type) {
                continue;
            }

            $stub = (new Stub(__DIR__.'/../../stubs/model/cast.stub'));

            $stub->setVariable('NAME', $column->getName());
            $stub->setVariable('CASTTYPE', $cast_type);

            $casts[] = $stub->fill()->getContent();
        }

        if ($casts === []) {
            return null;
        }

        $stub = new Stub(__DIR__.'/../../stubs/model/casts.stub');

        $stub->setVariable('CASTS', trim(implode('', $casts)));

        return $stub->fill()->getContent();
    }

    private function getRelationships(): ?string
    {
        $relationships = [];

        foreach ($this->table->getColumns() as $column) {
            foreach ($column->getRelationships() as $relationship) {

                $relationship_type = $relationship->getType();

                $file = $relationship_type->stubFile();

                $stub = (new Stub(__DIR__.'/../../stubs/model/'.$file));

                $table = $relationship->getTable();

                $table_name = $table->getName();
                $model_name = $table->getStudlyName();

                $stub->setVariable('NAME', $relationship_type->nameMethod($table_name));
                $stub->setVariable('MODEL', $model_name);

                $this->uses[] = $relationship_type->classRelationLaravel();
                $this->uses[] = "App\Models\\".$model_name;

                $relationships[] = $stub->fill()->getContent();
            }
        }

        if ($relationships === []) {
            return null;
        }

        return trim(implode('', $relationships));
    }
}
