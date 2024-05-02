<?php

namespace S4mpp\Laragenius\Tests\Unit;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Tests\TestCase;

class StubTest extends TestCase
{
	public function test_if_stub_is_stringable()
	{
		$stub = new Stub('use');

        $this->assertIsString((string)$stub);
	}

	public function test_fill()
	{
		$stub = new Stub('use');

        $stub->fill([
			'CLASS_PATH' => 'path_example'
		]);

		$this->assertStringContainsString('path_example', (string)$stub);
	}

	public function test_put()
	{
		$stub = new Stub('use');

        $stub->put('file-use');

		$this->assertFileExists(base_path('file-use.php'));
	}
}