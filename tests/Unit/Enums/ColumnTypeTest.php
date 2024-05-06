<?php

namespace S4mpp\Laragenius\Tests\Unit\Fields;

use S4mpp\Laragenius\Fields\Integer;
use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Enums\ColumnType;

class ColumnTypeTest extends TestCase
{
    public function test_class(): void
    {
        $enum = ColumnType::Integer;

        $this->assertEquals(Integer::class, $enum->class());
    }

    public function test_cast(): void
    {
        $this->assertEquals('date', ColumnType::Date->cast());
        $this->assertNull(ColumnType::Text->cast());
    }
}
