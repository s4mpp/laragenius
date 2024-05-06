<?php

namespace S4mpp\Laragenius\Tests\Unit\Generators;

use S4mpp\Laragenius\Schema\Table;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Generators\Seeder;

class SeederTest extends TestCase
{
    public function test_get_filename(): void
    {
        Schema::create('tbl-example', fn ($table) => $table->increments('id'));

        $seeder = new Seeder(new Table('tbl-example'));

        $this->assertEquals('Database\Seeders', $seeder->getNamespace());
        $this->assertEquals('TblExampleSeeder', $seeder->getFileName());
    }

    public function test_get_content(): void
    {
        Schema::create('mains', function ($table): void {
            $table->increments('id');
        });

        Schema::create('childs', function ($table): void {
            $table->foreignId('main_id')->references('id')->on('mains');
            $table->string('email');
        });

        Schema::create('sub_childs', function ($table): void {
            $table->string('child_email');
            $table->foreign('child_email')->references('email')->on('childs');
        });

        $seeder = new Seeder(new Table('childs'));

        $content = (string) $seeder->getContent();

        $this->assertEquals('stubs/seeder/seeder', $seeder->getContent()->getNameFile());
        $this->assertStringContainsString("for(Main::factory()->create(), 'main')", $content);
    }
}
