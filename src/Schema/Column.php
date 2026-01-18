<?php

namespace S4mpp\Laragenius\Schema;

use S4mpp\Laragenius\Enums\ColumnType;

class Column
{
    /** @var array<Relationship> */
    private array $relationships = [];

    public function __construct(private string $name, private bool $is_nullable, private bool $is_unique, private ColumnType $type)
    {
    }

    public function getType(): ColumnType
    {
        return $this->type;
    }

    /**
     * @return array<Relationship>
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    public function isUnique(): bool
    {
        return $this->is_unique;
    }

    public function isNullable(): bool
    {
        return $this->is_nullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addRelationship(Relationship $relationship): void
    {
        $this->relationships[] = $relationship;
    }
}
