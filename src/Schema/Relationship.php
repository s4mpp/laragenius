<?php

namespace S4mpp\Laragenius\Schema;

use S4mpp\Laragenius\Enums\RelationshipType;

class Relationship
{
    public function __construct(private string $table_name, private RelationshipType $type)
    {
    }

    public function getType(): RelationshipType
    {
        return $this->type;
    }

    public function getTableName(): string
    {
        return $this->table_name;
    }
}
