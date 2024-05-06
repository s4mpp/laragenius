<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Model;

class ModelTest extends TestCase
{
    public function test_get_filename(): void
    {
        Schema::create('table-example', fn ($table) => $table->increments('id'));

        $model = new Model(new Table('table-example'));

        $this->assertEquals('App\Models', $model->getNamespace());
        $this->assertEquals('TableExample', $model->getFileName());
    }

    public function test_create(): void
    {
        Schema::create('table-users', fn ($table) => $table->foreignId('user_Id')->references('id')->on('users'));

        $model = new Model(new Table('table-users'));

        $model->create();

        $this->assertFileExists(base_path('app/Models/TableUser.php'));
    }

    public function test_get_content(): void
    {
        Schema::create('examples', function ($table): void {
            $table->increments('id');
            $table->date('date');
            $table->foreignId('user_id')->references('id')->on('users');
        });

        Schema::create('childs', function ($table): void {
            $table->foreignId('example_id')->references('id')->on('examples');
        });

        $model = new Model(new Table('examples'));

        $content = (string) $model->getContent();

        $this->assertEquals('stubs/model/model', $model->getContent()->getNameFile());
        $this->assertStringContainsString('user(): BelongsTo', $content);
        $this->assertStringContainsString('childs(): HasMany', $content);
    }

    public function test_get_content_with_no_casts(): void
    {
        Schema::create('example', function ($table): void {
            $table->increments('id');
        });

        $model = new Model(new Table('example'));

        $this->assertStringNotContainsString('protected $casts', (string) $model->getContent());
    }
}
