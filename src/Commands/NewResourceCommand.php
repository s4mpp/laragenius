<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use S4mpp\Laragenius\Utils;

class NewResourceCommand extends Command
{
	protected $signature = 'laragenius:new-resource
                            {resource_name : Name of resource}
                            {--fields= : Fields of registers}
                            {--relations= : Relations of foreignk keys}
                            {--enums= : Enums to related fields}';

	protected $description = 'Create a new resource configuration file';

	public function handle(): void
    {
        $resource_name = $this->argument('resource_name');
        $fields = $this->option('fields') ?? '';
        $relations = $this->option('relations') ?? '';
        $enums = $this->option('enums') ?? '';

        $file_name = Str::snake(Str::lower($resource_name));
		
        $folder = 'laragenius';

		$file_path = $folder.DIRECTORY_SEPARATOR.$file_name.'.json';

        File::put($file_path, json_encode($this->_getFileStructure(
            $resource_name,
            $this->_getFields($fields),
            $this->_getRelations($relations),
            $this->_getEnums($resource_name, $enums),
        ), JSON_PRETTY_PRINT));
        
        $this->info("File ".$file_name.".json created.");
    }

    private function _getFileStructure(string $resource_name, array $fields, array $relations, array $enums)
    {
        return [
            'name' => Utils::nameModel($resource_name),
            'fields' => $fields,
            'relations' => $relations,
            'enums' => $enums,
        ];
    }

    private  function _getFields(string $fields = null)
    {
        $fields = array_filter(explode(',', $fields));

        $fields_mounted = [];
        
        foreach($fields as $field)
        {
            $exp = explode('.', $field);

            $name = $exp[0];
            $type = $exp[1] ?? 'string';

            if(!in_array($type, ['string', 'text', 'date', 'decimal', 'integer', 'tinyInteger', 'bigInteger']))
            {
                $this->error('Invalid field type for field '. $name);
                
                continue;
            }

            $fields_mounted[] = [
                'type' => Str::lower($type),
                'name' => Str::lower($name),
                'required' => true,
            ];
        }

        return $fields_mounted;
    }

    private  function _getRelations(string $relations = null)
    {
        $relations = array_filter(explode(',', $relations));

        $relations_mounted = [];
        
        foreach($relations as $model)
        {
            $relations_mounted[] = [
                'field' => Str::lower($model).'_id',
                'model' => Utils::nameModel($model),
                'type' => 'belongsTo',
            ];
        }

        return $relations_mounted;
    }

    private  function _getEnums(string $resource_name, string $enums = null)
    {
        $enums = array_filter(explode(',', $enums));

        $enums_mounted = [];
        
        foreach($enums as $field)
        {
            $enums_mounted[] = [
                'field' => $field,
                'enum' => Utils::nameModel($resource_name).Str::title($field)
            ];
        }

        return $enums_mounted;
    }
}