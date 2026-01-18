<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Seeder;

class SeederTest extends TestCase
{
    public function test_get_basic_data(): void
    {
        Schema::create('table-example', fn($table) => $table->increments('id'));

        $seeder = new Seeder(new Table('table-example'));

        $this->assertEquals('database/seeders', $seeder->getDestinationPath());
        $this->assertEquals('TableExampleSeeder', $seeder->getFilename());
        $this->assertStringContainsString('/../../stubs/seeder/seeder.stub', $seeder->getStubFile());
    }

    public function test_mount_file(): void
    {
        Schema::create('seeder-childs', function ($table): void {
            $table->increments('id');
        });

        $seeder = new Seeder(new Table('seeder-childs'));

        $stub = new Stub($seeder->getStubFile());

        $seeder->mountFile($stub);

        $content = $stub->fill()->getContent();

        $this->assertStringContainsString("class SeederChildSeeder extends Seeder", $content);
        $this->assertStringContainsString("SeederChild::factory()->count(10)->create()", $content);
    }
}
