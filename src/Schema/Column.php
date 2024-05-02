<?php

namespace S4mpp\Laragenius\Schema;

use S4mpp\Laragenius\Enums\ColumnType;

class Column
{
    private bool $nullable = false;

    private bool $unique = false;

    public function __construct(private string $name, private ColumnType $type)
    {
    }

    public function getType(): ColumnType
    {
        return $this->type;
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

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function setUnique(bool $is_unique): void
    {
        $this->unique = $is_unique;
    }
}
