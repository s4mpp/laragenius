<?php
namespace S4mpp\Laragenius\Generators;

use Illuminate\Support\Str;
use S4mpp\Laragenius\FileManipulation;
use S4mpp\Laragenius\Contracts\GeneratorInterface;

abstract class Generator implements GeneratorInterface
{
	protected string $studly_name;

	protected string $folder;
	
	public function __construct(private string $table_name) {
		$this->studly_name = Str::studly(Str::singular($table_name));
	}

	public function create(): string
	{
		$folder = $this->folder ?? null;
		
		return FileManipulation::putContentFile($this->getFileName(), $folder, $this->getContent());
	}

	protected function getStubVariables(): array
	{
		return [
			'STUDLY_NAME' => $this->studly_name,
			'NAMESPACE' => $this->getNamespace(),
		];
	}
	
	private function getContent(): ?string
	{
		$stub_file = $this->stub_file ?? null;

		if(!$stub_file)
		{
			return null;
		}

		return FileManipulation::getStubContents($stub_file, $this->getStubVariables());
	}
}