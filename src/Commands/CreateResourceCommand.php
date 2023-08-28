<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Resource;
use Illuminate\Console\Command;
use S4mpp\Laragenius\FileManipulation;

class CreateResourceCommand extends Command
{
	protected $signature = 'laragenius:create-resource
                            {resource_name : Name of resource}
							{--with-admin : Create admin panel resources}';

	protected $description = 'Generate files of new resource';

	private $resource;

	public function handle()
    {
        $resource_name = Str::lower($this->argument('resource_name'));

		$with_admin = $this->option('with-admin');

		$this->resource = FileManipulation::findResourceFile($resource_name);

        if(!$this->resource)
        {
            return $this->error('Config file of resource '.$resource_name.' not found');
        }

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
}