<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;
use S4mpp\Laragenius\FileManipulation;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;

class CreateResourceCommand extends Command
{
	protected $signature = 'lg:create';

	protected $description = 'Generate files of new resource';

	private array $files = [
		'model' => 'Model',
		'factory' => 'Factory',
		'seeder' => 'Seeder',
		'migration' => 'Migration',
		'enums' => 'Enums',
		'admin-resource' => 'Admin Resource'
	];

	public function handle()
    {
        $file_names = $this->_selectResource();

		$files = multiselect(label: 'Files to generate:', options: $this->files, default: array_keys($this->files), required: true, scroll: 6);

		foreach($file_names as $i => $file_name)
		{
			note('Generating resource '.$file_name);

			$resource_info = FileManipulation::findResourceFile($file_name);
	
			$resource = new Resource(
				$resource_info['name'] ?? '',
				$resource_info['title'] ?? '',
				$resource_info['fields'] ?? [],
				$resource_info['actions'] ?? [],
				$resource_info['relations'] ?? [],
				$resource_info['enums'] ?? []
			);

			if(in_array('model', $files))
			{
				$resource->createModel();
			}
			
			if(in_array('factory', $files))
			{
				$resource->createFactory();
			}
						
			if(in_array('seeder', $files))
			{
				$resource->createSeeder();
			}
						
			if(in_array('migration', $files))
			{
				$resource->createMigration($i);
			}
						
			if(in_array('enums', $files))
			{
				$resource->createEnums();
			}
	
			if(in_array('admin-resource', $files))
			{
				$resource->createAdminResource();
			}
		}
    }

	private function _selectResource()
	{
		$resources = collect(FileManipulation::getResourcesFiles())->sortBy('order');

		foreach($resources as $file => $resource)
		{
			$options[$file] = $resource->title.' ('.($resource->order ?? 0).')';
		}

		return multiselect(label: 'Select the resource(s) to create', options: $options ?? [], default: [], scroll: 15);
	}
}