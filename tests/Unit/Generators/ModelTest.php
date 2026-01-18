<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Model;

class ModelTest extends TestCase
{
    public function test_get_basic_data(): void
    {
        Schema::create('table-example', fn($table) => $table->increments('id'));

        $model = new Model(new Table('table-example'));

        $this->assertEquals('app/Models', $model->getDestinationPath());
        $this->assertEquals('TableExample', $model->getFilename());
        $this->assertStringContainsString('/../../stubs/model/model.stub', $model->getStubFile());
    }

    public function test_mount_file(): void
    {
        Schema::create('examples', function ($table): void {
            $table->increments('id');
            $table->datetime('date');
            $table->foreignId('user_id')->references('id')->on('users');
        });

        Schema::create('example-childs', function ($table): void {
            $table->foreignId('example_id')->references('id')->on('examples');
        });

        $model = new Model(new Table('examples'));

        $stub = new Stub($model->getStubFile());

        $model->mountFile($stub);

        $content = $stub->fill()->getContent();

        $this->assertStringContainsString("class Example extends Model", $content);
        $this->assertStringContainsString("'date' => 'datetime'", $content);
        $this->assertStringContainsString('user(): BelongsTo', $content);
        $this->assertStringContainsString('exampleChilds(): HasMany', $content);
    }
}
