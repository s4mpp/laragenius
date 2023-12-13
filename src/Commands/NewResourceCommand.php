<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use S4mpp\Laragenius\Utils;
use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

use Illuminate\Support\Facades\File;
use S4mpp\Laragenius\FileManipulation;
use function Laravel\Prompts\multiselect;
use Stichoza\GoogleTranslate\GoogleTranslate;

class NewResourceCommand extends Command
{
    protected $signature = 'lg:new';

	protected $description = 'Create a new resource configuration file';

    private $resource_loaded;

    private $translator;

    private string $folder;

	public function handle(): void
    {
        $this->folder = 'laragenius';

        $resource_name = text(label: 'Name of resource', placeholder: 'Ex.: User', required: true);

        $resource = FileManipulation::findResourceFile($resource_name.'.json');

        if($resource)
        {
            $this->resource_loaded = $resource;

            $this->info('Resource '.$resource_name.' loaded');
        }

        $this->_loadTranslator();
        
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
        
        $this->_makeDirectoryIfNotExists();

        $this->bar = $this->output->createProgressBar(1 + count($fields) + count($enums) + count($relations));

        $this->bar->setFormat('Gerando: [%bar%] %percent:3s%%');
 
        $this->bar->start();
        
        $file_structure = $this->_getFileStructure(
            $resource_name,
            $this->_createFields($fields),
            $actions,
            $this->_createRelations($resource_name, $relations),
            $this->_createEnums($resource_name, $enums),
        );

        $file_name = Str::snake(Str::lower($resource_name));

        $file_path = $this->folder.DIRECTORY_SEPARATOR.$file_name.'.json';

        File::put($file_path, json_encode($file_structure, JSON_PRETTY_PRINT));

        $this->bar->finish();
        
        info("File [".$this->folder."/".$file_name.".json] (".$file_structure['title'].") created.");
    }

    private function _loadTranslator()
    {
        if($this->resource_loaded)  
        {
            return;
        }

        if(!Utils::isInternetConnected())
        {            
            $this->warn("Is not possible translate the fields. (NO INTERNET)");

            return;
        }
        
        $this->translator = new GoogleTranslate('pt-br');
        $this->translator->setClient('webapp');
    }

    private function _makeDirectoryIfNotExists()
    {
		if(!File::exists($this->folder))
		{
            File::makeDirectory($this->folder);
            
			$this->info("Folder '{$this->folder}' created successfully.");
        }
    }

    private function _getTotalFilesGenerated(): int
    {
        $files = File::files($this->folder);

        return count($files) + 1;
    }

    private function _getFileStructure(string $resource_name, array $fields, array $actions, array $relations, array $enums)
    {
        $content = [
            'order' => ($this->resource_loaded) ? ($this->resource_loaded['order'] ?? 0) : $this->_getTotalFilesGenerated(),
            'name' => $resource_name,
            'title' => ($this->resource_loaded) ? $this->resource_loaded['title'] : Utils::translate(Str::plural($resource_name), $this->translator),
            'fields' => $fields,
            'actions' => $actions,
            'relations' => $relations,
            'enums' => $enums,
        ];

        return $content;
    }

    private function _collectFields()
    {
        foreach($this->resource_loaded['fields'] ?? [] as $field)
        {
            $type = ($field->type == 'string') ? null : '.'.$field->type;

            $default_fields[] = $field->name.$type;
        }

        $fields = text(
            label: 'Fields',
            placeholder: 'Separated by ","',
            required: true,
            default: isset($default_fields) ? join(',', $default_fields) : '',
            validate: function($value)
        {
            foreach(explode(',', $value) as $field)
            {
                $exp = explode('.', $field);
    
                $name = $exp[0];
                $type = $exp[1] ?? 'string';
    
                if(!in_array($type, ['string', 'text', 'date', 'datetime', 'decimal', 'integer', 'tinyInteger', 'bigInteger', 'boolean']))
                {
                    return 'Invalid field type for field '. $name;
                }
    
                if(in_array($name, ['id', 'created_at', 'updated_at']))
                {
                    return 'The field names "id", "created_at" and "updated_at" are prohibited';
                }
            }
        });

        return array_filter(explode(',', $fields));
    }

    private function _collectRelations()
    {
        foreach($this->resource_loaded['relations'] ?? [] as $relation)
        {
            $default_relations[] = $relation->model.'.'.$relation->fk_label;
        }

        $relations = text(
            label: 'Relations',
            placeholder: 'Separated by ","',
            default: isset($default_relations) ? join(',', $default_relations) : ''
        );

        return array_filter(explode(',', $relations));
    }

    private function _collectEnums()
    {
        foreach($this->resource_loaded['enums'] ?? [] as $enum)
        {
            $default_enums[] = $enum->id ?? null;
        }

        $enums = text(
            label: 'Enums',
            placeholder: 'Separated by ","',
            default: isset($default_enums) ? join(',', $default_enums) : ''
        );

        return array_filter(explode(',', $enums));
    }

    private  function _createFields(array $fields = [])
    {        
        foreach($fields as $field)
        {
            $field_loaded = null;

            $exp = explode('.', $field);

            $name = $exp[0];
            $type = $exp[1] ?? 'string';

            foreach($this->resource_loaded['fields'] ?? [] as $field)
            {
                if($field->name == $name)
                {
                    $field_loaded = $field;
                }
            }

            $fields_mounted[] = [
                'name' => Str::lower($name),
                'title' => $field_loaded ? ($field_loaded->title ?? $name) : Utils::translate($name, $this->translator),
                'type' => Str::lower($type),
                'required' => $field_loaded->required ?? true,
                'unique' => $field_loaded->unique ?? false,
            ];
            
            $this->bar->advance();
        }

        return $fields_mounted ?? [];
    }

    private  function _createRelations(string $resource_name, array $relations = [])
    {
        foreach($relations as $relation)
        {
            $relation_loaded = null;
            
            $exp = explode('.', $relation);

            $field_name = Str::lower($exp[0]).'_id';

            foreach($this->resource_loaded['relations'] ?? [] as $relation)
            {
                if($relation->field == $field_name)
                {
                    $relation_loaded = $relation;
                }
            }

            $relations_mounted[] = [
                'field' => $field_name,
                'title' => ($relation_loaded) ? ($relation_loaded->title ?? $exp[0]) : Utils::translate($exp[0], $this->translator),
                'model' => Str::ucfirst($exp[0]),
                'fk_label' => $exp[1] ?? 'id',
            ];

            $this->bar->advance();

            $this->_createChild($exp[0], $resource_name);
        }

        return $relations_mounted ?? [];
    }

    private function _createChild(string $child_model, string $resource_name)
    {
        $resource = FileManipulation::findResourceFile($child_model.'.json')->toArray();

        if(!$resource)
        {
            return;
        }

        if(!isset($resource['childs']))
        {
            $resource['childs'] = [];
        }

        $resource['childs'][] = [
            'model' => Str::ucfirst($resource_name),
            'name' => Str::camel(Str::plural($resource_name))
        ];

        $file_path = $this->folder.DIRECTORY_SEPARATOR.strtolower($child_model).'.json';

        File::put($file_path, json_encode($resource, JSON_PRETTY_PRINT));

        info("File [".$this->folder."/".$child_model.".json] (".$resource['title'].") updated.");
    }

    private  function _createEnums(string $resource_name, array $enums = [])
    {
        foreach($enums as $field)
        {
            $enum_loaded = null;

            $field_name = Str::snake($field);

            foreach($this->resource_loaded['enums'] ?? [] as $enum)
            {
                if($enum->field == $field_name)
                {
                    $enum_loaded = $enum;
                }
            }

            $enums_mounted[] = [
                'id' => Str::ucfirst($field_name),
                'field' => $field_name,
                'title' => ($enum_loaded) ? $enum_loaded->title : Utils::translate($field_name, $this->translator),
                'enum' => $resource_name.$field
            ];

            $this->bar->advance();
        }

        return $enums_mounted ?? [];
    }
}