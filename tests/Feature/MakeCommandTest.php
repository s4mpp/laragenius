<?php

namespace S4mpp\Laragenius\Tests\Feature;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Model;
use S4mpp\Laragenius\Generators\Seeder;
use S4mpp\Laragenius\Generators\Factory;
use Orchestra\Testbench\Concerns\WithWorkbench;

class MakeCommandTest extends TestCase
{
    use WithWorkbench;

    public function setUp(): void
    {
        parent::setUp();

        Laragenius::flushGenerators();
    }

    //TODO generate one test for each generator
    public function test_make_command(): void
    {
        Schema::create('examples', function ($table): void {
            $table->increments('id');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('name');
            $table->date('date');
            $table->datetime('datetime')->nullable();
            $table->decimal('value', 10, 2);
            $table->integer('order');
            $table->tinyInteger('level');
            $table->text('bio');
            $table->string('password');
            $table->string('token')->unique();
            $table->string('phone');
            $table->string('field')->unique();
            $table->binary('file')->nullable();
            $table->binary('photo');
            $table->string('email');

            $table->index(['name']);
        });

        Schema::create('table_example_childs', function ($table): void {
            $table->increments('id');
            $table->foreignId('table_example_id')->references('id')->on('examples');
            $table->foreignId('table_example_email')->references('email')->on('examples');
        });

        $command = $this->artisan('lg:make', ['table' => 'examples', '--force' => true]);

        $command->expectsChoice('Select the resources', [Model::class, Seeder::class, Factory::class], array_merge(['', 0, 1, 2, 'None'], Laragenius::getGenerators()))
            ->expectsOutputToContain('File [app/Models/Example.php] created.')
            ->expectsOutputToContain('File [database/seeders/ExampleSeeder.php] created.')
            ->expectsOutputToContain('File [database/factories/ExampleFactory.php] created.')
            ->assertSuccessful();

        //TODO test content of files
        $this->assertFileExists(base_path('app/Models/Example.php'));
        $this->assertFileExists(base_path('database/seeders/ExampleSeeder.php'));
        $this->assertFileExists(base_path('database/factories/ExampleFactory.php'));
    }

    public function test_make_command_with_table_nonexistent(): void
    {
        $command = $this->artisan('lg:make', ['table' => 'xxxxxx']);

        $command->expectsOutputToContain('Table xxxxxx not found')->doesntExpectOutputToContain('created')->assertFailed();
    }

    public function test_select_invalid_resource(): void
    {
        Laragenius::addGenerator(Stub::class);

        $command = $this->artisan('lg:make', ['table' => 'users']);

        $command->expectsChoice('Select the resources', [Stub::class], array_merge(['', 0, 1, 2, 3, 'None'], Laragenius::getGenerators()))
            ->expectsOutputToContain('is not a generator')->doesntExpectOutputToContain('created')->assertFailed();
    }
}
