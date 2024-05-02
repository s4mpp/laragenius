<?php

namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Stub;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Contracts\GeneratorInterface;

abstract class Generator implements GeneratorInterface
{
    protected string $folder;

    /**
     * @var array<string>
     */
    private array $uses = [];

    public function __construct(private Table $table)
    {
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    protected function addUse(string $class): void
    {
        $this->uses[] = $class;
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
            'USES' => $this->getUses(),
        ];
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
