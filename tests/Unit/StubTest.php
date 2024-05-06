<?php

namespace S4mpp\Laragenius\Tests\Unit;

use ErrorException;
use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Tests\TestCase;

class StubTest extends TestCase
{
    public function test_if_stub_is_stringable(): void
    {
        $stub = new Stub('stubs/use');

        $this->assertIsString((string) $stub);
    }

    public function test_with_nonexistent_file(): void
    {
        $this->expectException(ErrorException::class);

        new Stub('stubs/xxxxxxx');
    }

    public function test_fill(): void
    {
        $stub = new Stub('stubs/use');

        $stub->fill([
            'CLASS_PATH' => 'path_example',
        ]);

        $this->assertStringContainsString('path_example', (string) $stub);
    }

    public function test_put(): void
    {
        $stub = new Stub('stubs/use');

        $stub->put('file-use');

        $this->assertFileExists(base_path('file-use.php'));
    }
}
