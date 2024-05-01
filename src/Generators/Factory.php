<?php
namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Generators\Generator;

final class Factory extends Generator
{
	protected string $folder = 'database/factories';

	protected string $stub_file = 'factory';

	public function getNamespace(): string
	{
		return 'Database\Factories';
	}

	public function getFilename(): string
	{
		return $this->studly_name.'Factory';
	}
}
