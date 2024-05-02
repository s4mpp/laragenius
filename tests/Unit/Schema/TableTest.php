<?php

namespace S4mpp\Laragenius\Tests\Unit\Schema;

use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;

class TableTest extends TestCase
{
	public function test_create_instance()
	{
		Schema::create('table-example', function($table) {
			$table->string('name')->nullable()->unique();
			$table->string('email')->index('index-example');
		});

		$table = new Table('table-example');

		$this->assertEquals('table-example', $table->getName());
		
		$columns = $table->getColumns();

		$first_column = $columns[0];

		$this->assertCount(2, $columns);
		$this->assertContainsOnlyInstancesOf(Column::class, $columns);

		$this->assertEquals('name', $first_column->getName());
		$this->assertTrue($first_column->isNullable());
		$this->assertTrue($first_column->isUnique());
	}
}