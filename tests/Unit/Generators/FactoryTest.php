<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Factory;

class FactoryTest extends TestCase
{
    public function test_get_filename(): void
    {
        Schema::create('table-example', fn ($table) => $table->increments('id'));

        $factory = new Factory(new Table('table-example'));

        $this->assertEquals('Database\Factories', $factory->getNamespace());
        $this->assertEquals('TableExampleFactory', $factory->getFileName());
    }

    public function test_get_folder(): void
    {
        Schema::create('tbl_example_6', fn ($table) => $table->increments('id'));

        $factory = new Factory(new Table('tbl_example_6'));

        $this->assertEquals('database/factories', $factory->getFolder());
    }

    public function test_get_table(): void
    {
        Schema::create('table-example', fn ($table) => $table->increments('id'));

        $factory = new Factory(new Table('table-example'));

        $this->assertInstanceOf(Table::class, $factory->getTable());
    }

    public function test_get_content(): void
    {
        Schema::create('example', function ($table): void {
            $table->binary('file');
            $table->date('date');
            $table->string('unique')->unique();
        });

        $factory = new Factory(new Table('example'));

        $this->assertStringContainsString('Factory extends Factory', $factory->getContent());
    }
}
