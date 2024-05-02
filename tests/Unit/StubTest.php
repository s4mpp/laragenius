<?php

namespace S4mpp\Laragenius\Tests\Unit;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Tests\TestCase;

class StubTest extends TestCase
{
	public function test_if_stub_is_stringable()
	{
		$stub = new Stub('model');

        $this->assertIsString((string)$stub);
	}

	public function test_fill()
	{
		$stub = new Stub('model');

        $stub->fill([
			'NAMESPACE' => 'namespace_example'
		]);

		$this->assertStringContainsString('namespace_example', (string)$stub);
	}

	public function test_put()
	{
		$stub = new Stub('model');

        $stub->put('file-model');

		$this->assertFileExists(base_path('file-model.php'));
	}
}