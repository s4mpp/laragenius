<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;
use S4mpp\Laragenius\FileManipulation;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class CreateResourceCommand extends Command
{
	protected $signature = 'lg:create';

	protected $description = 'Generate files of new resource';

	private $resource;

	public function handle()
    {
        $resource_file = $this->_selectResource();

		$this->resource = FileManipulation::findResourceFile($resource_file);

		$with_admin = confirm('Generate Admin Resource?', default: true);

		$resource = new Resource(
			$this->resource['name'],
			$this->resource['title'],
			$this->resource['fields'],
			$this->resource['actions'],
			$this->resource['relations'],
			$this->resource['enums']
		);

		$resource->createModel();
		
		$resource->createFactory();
		
		$resource->createSeeder();
		
		$resource->createMigration();
		
		$resource->createEnums();

		if($with_admin)
		{
		 	$resource->createAdminResource();
		}
    }

	private function _selectResource()
	{
		$resources = FileManipulation::getResourcesFiles();

		foreach($resources as $file => $resource)
		{
			$options[$file] = $resource->title;
		}

		return select(label: 'Select a resource to create', options: $options ?? []);
	}
}