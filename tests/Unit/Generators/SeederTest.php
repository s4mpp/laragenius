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

class SeederTest extends TestCase
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
		$seeder = new Seeder(new Table('table-example'));

		$this->assertEquals('Database\Seeders', $seeder->getNamespace());
		$this->assertEquals('TableExampleSeeder', $seeder->getFileName());
	}

	public function test_get_content()
	{
		$seeder = new Seeder(new Table('table-example'));

		$this->assertSame('seeder', $seeder->getContent()->getNameFile());
	}

}