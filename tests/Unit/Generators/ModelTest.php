<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Schema\Column;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\ColumnType;
use S4mpp\Laragenius\Generators\Model;

class ModelTest extends TestCase
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
		$model = new Model(new Table('table-example'));

		$this->assertEquals('App\Models', $model->getNamespace());
		$this->assertEquals('TableExample', $model->getFileName());
	}

	public function test_get_content()
	{
		$model = new Model(new Table('table-example'));

		$this->assertSame('model', $model->getContent()->getNameFile());
	}

}