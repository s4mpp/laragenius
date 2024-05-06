<?php

namespace S4mpp\Laragenius\Schema;

use S4mpp\Laragenius\Enums\ColumnType;

class Column
{
    private bool $nullable = false;

    private bool $unique = false;

    /** @var array<Relationship> */
    private array $relationships = [];

    public function __construct(private string $name, private ?ColumnType $type = null)
    {
    }

    public function getType(): ?ColumnType
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
        return $this->unique;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addRelationship(Relationship $relationship): void
    {
        $this->relationships[] = $relationship;
    }

    public function setNullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function setUnique(bool $is_unique): self
    {
        $this->unique = $is_unique;

        return $this;
    }
}
