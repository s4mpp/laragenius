<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;
use S4mpp\Laragenius\FileManipulation;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;

class CreateResourceCommand extends Command
{
	protected $signature = 'lg:create';

	protected $description = 'Generate files of new resource';

	public function handle()
    {
        $file_names = $this->_selectResource();

		$with_admin = confirm('Generate Admin Resource?', default: true);

		foreach($file_names as $file_name)
		{
			note('Generating resource '.$file_name.'.json');

			$resource_info = FileManipulation::findResourceFile($file_name);
	
			$resource = new Resource(
				$resource_info['name'],
				$resource_info['title'],
				$resource_info['fields'],
				$resource_info['actions'],
				$resource_info['relations'],
				$resource_info['enums']
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
    }

	private function _selectResource()
	{
		$resources = FileManipulation::getResourcesFiles();

		foreach($resources as $file => $resource)
		{
			$options[$file] = $resource->title;
		}

		return multiselect(label: 'Select the resource(s) to create', options: $options ?? []);
	}
}