<?php

namespace S4mpp\Laragenius\Tests\Feature;

use stdClass;
use S4mpp\Laragenius\Stub;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Model;
use Illuminate\Support\Facades\Storage;
use S4mpp\Laragenius\Generators\Seeder;
use S4mpp\Laragenius\Generators\Factory;
use Workbench\App\Laragenius\CustomGenerator;
use Orchestra\Testbench\Concerns\WithWorkbench;

class MakeCommandTest extends TestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Laragenius::flushGenerators();
    }

    public static function generatorProvider(): array
    {
        return [
            'model' => [Model::class, 'app/Models/Example.php', 'Example'],
            'seeder' => [Seeder::class, 'database/seeders/ExampleSeeder.php', 'ExampleSeeder'],
            'factory' => [Factory::class, 'database/factories/ExampleFactory.php', 'ExampleFactory'],
        ];
    }

    /**
     * @dataProvider generatorProvider
     */
    public function test_make_command(string $generator, string $file, string $class_name): void
    {
        Schema::create('examples', function ($table): void {
            $table->increments('id');
        });

        $command = $this->artisan('lg:make', ['table_name' => 'examples', '--force' => true]);

        $command->expectsChoice(
            question: 'Selecione os geradores',
            answer: $generator,
            answers: array_merge(['', 0, 1, 2, 'None'], Laragenius::getGenerators()),
            strict: true,
        )
            ->expectsOutputToContain('Arquivo [' . $file . '] criado.')
            ->assertSuccessful();
    }

    public function test_make_command_with_table_nonexistent(): void
    {
        $command = $this->artisan('lg:make', ['table_name' => 'xxxxxx']);

        $command->expectsOutputToContain('Tabela [xxxxxx] não encontrada')->assertFailed();
    }

    public function test_select_invalid_resource(): void
    {
        Laragenius::addGenerator(stdClass::class);

        $command = $this->artisan('lg:make', ['table_name' => 'users']);

        $command->expectsChoice(
            question: 'Selecione os geradores',
            answer: [stdClass::class],
            answers: array_merge(['', 0, 1, 2, 3, 'None'], Laragenius::getGenerators()),
            strict: true,
        )->expectsOutputToContain('não é um gerador válido')->assertFailed();
    }

    public function test_do_not_overwrite_existing_file(): void
    {
        $table_name = fake()->word();

        Schema::create($table_name, function ($table): void {
            $table->increments('id');
        });

        $path = base_path('app/Generated/CustomGenerated.php');

        file_put_contents($path, 'fake content');

        $command = $this->artisan('lg:make', ['table_name' => $table_name]);

        $command->expectsChoice(
            question: 'Selecione os geradores',
            answer: [CustomGenerator::class],
            answers: array_merge(['', 0, 1, 2, 'None'], Laragenius::getGenerators()),
            strict: true,
        )->expectsOutputToContain('Arquivo [app/Generated/CustomGenerated.php] já existe')->assertFailed();

        $this->assertStringContainsString('fake content', file_get_contents($path));
    }
}
