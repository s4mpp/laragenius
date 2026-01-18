<?php

namespace S4mpp\Laragenius\Schema;

use S4mpp\Laragenius\Enums\RelationshipType;

class Relationship
{
    public function __construct(private Table $table, private RelationshipType $type)
    {
    }

    public function getType(): RelationshipType
    {
        return $this->type;
    }

    public function getTable(): Table
    {
        return $this->table;
    }
}
