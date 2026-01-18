<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Model;
use S4mpp\Laragenius\Generators\Factory;

class FactoryTest extends TestCase
{
    public function test_get_basic_data(): void
    {
        Schema::create('table-example', fn($table) => $table->increments('id'));

        $factory = new Factory(new Table('table-example'));

        $this->assertEquals('database/factories', $factory->getDestinationPath());
        $this->assertEquals('TableExampleFactory', $factory->getFilename());
        $this->assertStringContainsString('/../../stubs/factory/factory.stub', $factory->getStubFile());
    }

    public function test_mount_file(): void
    {
        Schema::create('examples', function ($table): void {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->foreignId('user_id')->references('id')->on('users');
        });

        $factory = new Factory(new Table('examples'));

        $stub = new Stub($factory->getStubFile());

        $factory->mountFile($stub);

        $content = $stub->fill()->getContent();

        $this->assertStringContainsString("class ExampleFactory extends Factory", $content);
        $this->assertStringContainsString("'name' => fake()->word(), /** (optional) */", $content);
        $this->assertStringContainsString("'email' => fake()->unique()->word(),", $content);
        $this->assertStringContainsString("'user_id' => User::factory()", $content);
        $this->assertStringContainsString("use App\Models\User;", $content);
    }
}
