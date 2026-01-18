<?php

namespace S4mpp\Laragenius\Tests\Unit\Schema;

use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Enums\ColumnType;

class ColumnTest extends TestCase
{
    public function test_create_instance(): void
    {
        $column = new Column('name', false, false, ColumnType::Varchar);

        $this->assertEquals('name', $column->getName());
        $this->assertFalse($column->isUnique());
        $this->assertFalse($column->isNullable());
        $this->assertEquals(ColumnType::Varchar, $column->getType());

        //TODO test get relationships
    }
}
