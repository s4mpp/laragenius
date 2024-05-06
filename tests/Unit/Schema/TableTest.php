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
        $this->assertEquals('Example', $table->getModelName());
    }

    public function test_create_instance_with_nonexistent_table(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Table xxxxxx not found');

        $table = new Table('xxxxxx');
    }

    public function test_to_model_name(): void
    {
        $model_name = Table::toModelName('table-examples');

        $this->assertEquals('TableExample', $model_name);
    }

    public function test_load_columns(): void
    {
        Schema::create('tbl_example_1', function ($table): void {
            $table->string('name')->nullable();
        });

        $table = new Table('tbl_example_1');

        $table->loadColumns();

        $columns = $table->getColumns();

        $first_column = $columns['name'];

        $this->assertCount(1, $columns);
        $this->assertContainsOnlyInstancesOf(Column::class, $columns);

        $this->assertEquals('name', $first_column->getName());
        $this->assertTrue($first_column->isNullable());
        $this->assertfalse($first_column->isUnique());
    }

    public function test_load_columns_no_filter(): void
    {
        Schema::create('table-example', function ($table): void {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $table = new Table('table-example');

        $table->loadColumns();

        $columns = $table->getColumns(filter: false);

        $this->assertCount(4, $columns);
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('created_at', $columns);
        $this->assertArrayHasKey('updated_at', $columns);
    }

    public function test_load_uniques(): void
    {
        Schema::create('table-example-uniques', function ($table): void {
            $table->string('name');
            $table->string('email')->unique();

            $table->index(['name']);
        });

        $table = new Table('table-example-uniques');

        $table->loadColumns()->loadUniqueIndexes();

        $columns = $table->getColumns();

        $this->assertFalse($columns['name']->isUnique());
        $this->assertTrue($columns['email']->isUnique());
    }

    public function test_load_relationship_belongs_to(): void
    {
        Schema::create('tbl_belongs_to_relationships', function ($table): void {
            $table->foreignId('user_id')->references('id')->on('users');
        });

        $table = new Table('tbl_belongs_to_relationships');

        $table->loadColumns()->loadRelationships();

        $columns = $table->getColumns();

        $relationship = $columns['user_id']->getRelationships()[0];

        $this->assertEquals('users', $relationship->getTableName());
        $this->assertEquals(RelationshipType::BelongsTo, $relationship->getType());
    }

    public function test_load_relationship_has_many(): void
    {
        Schema::create('tbl_has_many_relationships', function ($table): void {
            $table->increments('id');
        });

        Schema::create('tbl_has_many_example_childs', function ($table): void {
            $table->foreignId('table_example_id')->references('id')->on('tbl_has_many_relationships');
        });

        $table = new Table('tbl_has_many_relationships');

        $table->loadColumns()->loadRelationships();

        $columns = $table->getColumns(filter: false);

        $relationship = $columns['id']->getRelationships()[0];

        $this->assertEquals('tbl_has_many_example_childs', $relationship->getTableName());
        $this->assertEquals(RelationshipType::HasMany, $relationship->getType());
    }

    public function test_load_relationships_and_uniques_without_columns(): void
    {
        Schema::create('table_relationships', function ($table): void {
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('email')->unique();
        });

        Schema::create('table_example_childs', function ($table): void {
            $table->foreignId('table_example_id')->references('id')->on('table_relationships');
        });

        $table = new Table('table_relationships');

        $table->loadUniqueIndexes();
        $table->loadRelationships();

        $this->assertEmpty($table->getColumns());
    }
}
