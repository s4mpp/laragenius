<?php

namespace S4mpp\Laragenius\Tests\Unit;

use ErrorException;
use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

class StubTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $filesystem = new Filesystem;

        $filesystem->ensureDirectoryExists(base_path('path/to'));

        $filesystem->put(base_path('path/to/file.stub'), 'stub {{ KEY }}');
    }

    public function test_create_instance(): void
    {
        $stub = new Stub(base_path('path/to/file.stub'));

        $this->assertEquals('stub {{ KEY }}', $stub->getContent());
    }

    public function test_set_variable(): void
    {
        $stub = new Stub(base_path('path/to/file.stub'));

        $stub->setVariable('key', 'value');

        $this->assertEquals(['key' => 'value'], $stub->getVariables());
    }

    public function test_fill(): void
    {
        $word = fake()->word();

        $stub = new Stub(base_path('path/to/file.stub'));

        $stub->setVariable('KEY', $word);

        $stub->fill();

        $this->assertEquals('stub ' . $word, $stub->getContent());
    }
}
