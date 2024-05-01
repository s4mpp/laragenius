<?php
namespace S4mpp\Laragenius\Generators;

use S4mpp\Laragenius\Generators\Generator;

final class Seeder extends Generator
{
	protected string $folder = 'database/seeders';

	protected string $stub_file = 'seeder';

	public function getNamespace(): string
	{
		return 'Database\Seeders';
	}

	public function getFilename(): string
	{
		return $this->studly_name.'Seeder';
	}
}
