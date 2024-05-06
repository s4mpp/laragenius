<?php

namespace S4mpp\Laragenius\Tests\Unit\Schema;

use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Enums\ColumnType;

class ColumnTest extends TestCase
{
    public function test_create_instance(): void
    {
        $column = new Column('name', ColumnType::Varchar);

        $this->assertEquals('name', $column->getName());
        $this->assertEquals(ColumnType::Varchar, $column->getType());
    }

    public function test_unique(): void
    {
        $column = new Column('name', ColumnType::Varchar);

        $column->setUnique(true);

        $this->assertTrue($column->isUnique());
    }

    public function test_nullable(): void
    {
        $column = new Column('name', ColumnType::Varchar);

        $column->setNullable(false);

        $this->assertFalse($column->isNullable());
    }
}
