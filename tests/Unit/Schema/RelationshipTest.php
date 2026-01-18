<?php

namespace S4mpp\Laragenius\Tests\Unit\Schema;

use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Schema\Relationship;
use S4mpp\Laragenius\Enums\RelationshipType;

class RelationshipTest extends TestCase
{
    public function test_create_instance(): void
    {
        $relationship = new Relationship(new Table('table-x'), RelationshipType::BelongsTo);

        $this->assertEquals(RelationshipType::BelongsTo, $relationship->getType());
        $this->assertInstanceOf(Table::class, $relationship->getTable());
    }
}
