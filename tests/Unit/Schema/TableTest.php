<?php

namespace S4mpp\Laragenius\Tests\Unit\Schema;

use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\RelationshipType;

class TableTest extends TestCase
{
    public function test_create_instance(): void
    {
        Schema::create('examples', fn ($table) => $table->increments('id'));

        $table = new Table('examples');

        $this->assertEquals('examples', $table->getName());
        $this->assertEquals('Example', $table->getStudlyName());

        $columns = $table->getColumns();

        $this->assertCount(1, $columns);
        $this->assertContainsOnlyInstancesOf(Column::class, $columns);
    }
}
