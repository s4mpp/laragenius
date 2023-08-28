<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Utils;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;

use Illuminate\Support\Facades\File;
use S4mpp\Laragenius\FileManipulation;
use function Laravel\Prompts\multiselect;

class NewResourceCommand extends Command
{
    protected $signature = 'laragenius:new-resource';

	protected $description = 'Create a new resource configuration file';

    private $resource_loaded;

	public function handle(): void
    {
        $resource_name = text(label: 'Name of resource', placeholder: 'Ex.: User', required: true);

        $resource = FileManipulation::findResourceFile($resource_name);

        if($resource)
        {
            $this->resource_loaded = $resource;

            $this->info('Resource '.$resource_name.' loaded');
        }
        
        $fields = $this->_collectFields();
        
        $actions = multiselect(label: 'Actions', options: [
            'create' => 'Create',
            'read' => 'Read',
            'update' => 'Update',
            'delete' => 'Delete'
        ],
        default: ($this->resource_loaded) ? $this->resource_loaded['actions'] : ['create', 'update']);

        $relations = $this->_collectRelations();
        
        $enums = $this->_collectEnums();
        
        $file_structure = $this->_getFileStructure(
            $resource_name,
            $this->_getFields($fields),
            $actions,
            $this->_getRelations($relations),
            $this->_getEnums($resource_name, $enums),
        );

        $file_name = Str::snake(Str::lower($resource_name));
        
        $folder = 'laragenius';
        
        $this->_makeDirectoryIfNotExists($folder);
        
        $file_path = $folder.DIRECTORY_SEPARATOR.$file_name.'.json';

        File::put($file_path, json_encode($file_structure, JSON_PRETTY_PRINT));
        
        $this->info("File [".$folder."/".$file_name.".json] created.");
    }

    private function _makeDirectoryIfNotExists(string $folder_name)
    {
		if(!File::exists($folder_name))
		{
            File::makeDirectory($folder_name);
            
			$this->info("Folder '{$folder_name}' created successfully.");
        }
    }

    private function _getFileStructure(string $resource_name, array $fields, array $actions, array $relations, array $enums)
    {
        return [
            'name' => $resource_name,
            'title' => Str::ucfirst(Str::replace('_', ' ', Utils::nameTable($resource_name))),
            'fields' => $fields,
            'actions' => $actions,
            'relations' => $relations,
            'enums' => $enums,
        ];
    }

    private function _collectFields()
    {
        foreach($this->resource_loaded['fields'] as $field)
        {
            $type = ($field->type == 'string') ? null : '.'.$field->type;

            $default_fields[] = $field->name.$type;
        }

        return text(
            label: 'Fields',
            placeholder: 'Separated by ","',
            required: true,
            default: isset($default_fields) ? join(',', $default_fields) : null,
            validate: function($value)
        {
            foreach(explode(',', $value) as $field)
            {
                $exp = explode('.', $field);
    
                $name = $exp[0];
                $type = $exp[1] ?? 'string';
    
                if(!in_array($type, ['string', 'text', 'date', 'decimal', 'integer', 'tinyInteger', 'bigInteger', 'boolean']))
                {
                    return 'Invalid field type for field '. $name;
                }
    
                if(in_array($name, ['id', 'created_at', 'updated_at']))
                {
                    return 'The field names "id", "created_at" and "updated_at" are prohibited';
                }
            }
        });
    }

    private function _collectRelations()
    {
        foreach($this->resource_loaded['relations'] as $relation)
        {
            $default_relations[] = $relation->model.'.'.$relation->fk_label;
        }

        return text(
            label: 'Relations',
            placeholder: 'Separated by ","',
            default: isset($default_relations) ? join(',', $default_relations) : null
        );
    }

    private function _collectEnums()
    {
        foreach($this->resource_loaded['enums'] as $enum)
        {
            $default_enums[] = $enum->title ?? null;
        }

        return text(
            label: 'Enums',
            placeholder: 'Separated by ","',
            default: isset($default_enums) ? join(',', $default_enums) : null
        );
    }

    private  function _getFields(string $fields = null)
    {
        $fields = array_filter(explode(',', $fields));
        
        foreach($fields as $field)
        {
            $exp = explode('.', $field);

            $name = $exp[0];
            $type = $exp[1] ?? 'string';

            $fields_mounted[] = [
                'name' => Str::lower($name),
                'type' => Str::lower($type),
                'required' => true,
            ];
        }

        return $fields_mounted ?? [];
    }

    private  function _getRelations(string $relations = null)
    {
        $relations = array_filter(explode(',', $relations));
        
        foreach($relations as $relation)
        {
            $exp = explode('.', $relation);

            $relations_mounted[] = [
                'field' => Str::lower($exp[0]).'_id',
                'model' => $exp[0],
                'fk_label' => $exp[1] ?? 'id',
                'type' => 'belongsTo',
            ];
        }

        return $relations_mounted ?? [];
    }

    private  function _getEnums(string $resource_name, string $enums = null)
    {
        $enums = array_filter(explode(',', $enums));
        
        foreach($enums as $field)
        {
            $enums_mounted[] = [
                'field' => Str::snake($field),
                'title' => $field,
                'enum' => $resource_name.$field
            ];
        }

        return $enums_mounted ?? [];
    }
}