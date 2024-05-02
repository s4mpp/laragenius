<?php

namespace S4mpp\Laragenius\Generators;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Contracts\GeneratorInterface;

abstract class Generator implements GeneratorInterface
{
    protected string $folder;

    public function __construct(private Table $table)
    {
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function create(): string
    {
        $folder = $this->folder ?? null;

        $stub = $this->getContent();

        $stub->fill($this->getStubVariables());

        return $stub->put($this->getFileName(), $folder);
    }

    /**
     * @return array<string>
     */
    protected function getStubVariables(): array
    {
        return [
            'STUDLY_NAME' => Table::toModelName($this->table->getName()),
            'NAMESPACE' => $this->getNamespace(),
        ];
    }
}
