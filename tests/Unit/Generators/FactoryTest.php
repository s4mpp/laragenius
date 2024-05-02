<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\ColumnType;
use S4mpp\Laragenius\Generators\Model;
use S4mpp\Laragenius\Generators\Seeder;
use S4mpp\Laragenius\Generators\Factory;

class FactoryTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		Schema::create('table-example', function($table) {
			$table->string('field-example');
		});
	}

	public function test_get_filename()
	{
		$factory = new Factory(new Table('table-example'));

		$this->assertEquals('Database\Factories', $factory->getNamespace());
		$this->assertEquals('TableExampleFactory', $factory->getFileName());
	}

	public function test_get_content()
	{
		$factory = new Factory(new Table('table-example'));

		$this->assertSame('factory/factory', $factory->getContent()->getNameFile());
	}
}