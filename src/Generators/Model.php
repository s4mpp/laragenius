<?php
namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Generators\Generator;

final class Model extends Generator
{
	protected string $folder = 'app/Models';

	protected string $stub_file = 'model';

	public function getNamespace(): string
	{
		return 'App\Models';
	}

	public function getFilename(): string
	{
		return $this->studly_name;
	}
}
