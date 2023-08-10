<?php
namespace S4mpp\Laragenius\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use S4mpp\Laragenius\Utils;
use Illuminate\Support\Facades\File;
use S4mpp\Laragenius\FileManipulation;

class CreateResourceCommand extends Command
{
	protected $signature = 'laragenius:create-resource
                            {resource_name : Name of resource}
							{--with-admin : Create admin panel resources}';

	protected $description = 'Generate files of new resource';

	public function handle()
    {
        $resource_name = Str::lower($this->argument('resource_name'));

		$with_admin_resources = $this->option('with-admin');

		$file_config = 'laragenius'.DIRECTORY_SEPARATOR.$resource_name.'.json';

		if(!file_exists($file_config))
		{
			return $this->error('Config file '.$resource_name.'.json not found');
		}

		$config = json_decode(file_get_contents($file_config));

		$this->_createModel($config->name, $config->relations, $config->fields, $config->enums);
		
		$this->_createFactory($config->name, $config->fields, $config->relations, $config->enums);
		
		$this->_createSeeder($config->name);
		
		$this->_createMigration($config->name, $config->fields, $config->relations, $config->enums);
		
		$this->_createEnums($config->enums);

		if($with_admin_resources)
		{
			$this->_createAdminResource($config->name, $config->title ?? $config->name, $config->fields, $config->enums, $config->relations, $config->actions ?? []);
		}
    }

	private function _createAdminResource(string $name, string $title, array $fields, array $enums, array $relations, array $actions)
	{
		$uses = [
			'use S4mpp\AdminPanel\Form\Row;',
			'use S4mpp\AdminPanel\Form\Field;',
			'use S4mpp\AdminPanel\Table\Column;',
			'use S4mpp\AdminPanel\Resources\Resource;',
		];

		$table_fields = $form_fields = [];

		foreach($relations as $relation)
		{
			$uses[] = "use App\Models\\".$relation->model.';';

			$table_fields[] = FileManipulation::getStubContents('admin_resource_table_column', [
				'TITLE'  => Str::replace(['_id', '_'], ['', ' '], ucfirst($relation->field)),
				'NAME'  => Str::replace('_id', '', $relation->field),
				'MODIFIERS' => "->relation('".($relation->fk_label ?? 'id')."')",
			]);

			$form_fields[] = FileManipulation::getStubContents('admin_resource_form_field', [
				'TITLE'  => Str::replace(['_id', '_'], ['', ' '], ucfirst($relation->field)),
				'NAME'  => $relation->field,
				'MODIFIERS' => '->relation('.$relation->model."::all(), '".($relation->fk_label ?? 'id')."')",
				'NOT_REQUIRED' => null
			]);
		}

		foreach($fields as $field)
		{
			$field_modifiers = $table_modifiers = [];

			switch($field->type)
			{
				case 'date':
					$field_modifiers[] = '->date()';
					$table_modifiers[] = "->datetime('d/m/Y')";
					break;
				
				case 'decimal':
					$field_modifiers[] = '->decimal()->min(0.1)';
					break;
				
				case 'integer':
				case 'tinyInteger':
				case 'bigInteger':
					$field_modifiers[] = '->integer()->min(1)';
					break;
				
				case 'text':
					$field_modifiers[] = '->textarea()->rows(4)';
					break;
			}

			$table_fields[] = FileManipulation::getStubContents('admin_resource_table_column', [
				'TITLE'  => Str::replace('_', ' ', ucfirst($field->name)),
				'NAME'  => $field->name,
				'MODIFIERS' => join('', $table_modifiers),
			]);
			
			$form_fields[] = FileManipulation::getStubContents('admin_resource_form_field', [
				'TITLE'  => Str::replace('_', ' ', ucfirst($field->name)),
				'NAME'  => $field->name,
				'MODIFIERS' => join('', $field_modifiers),
				'NOT_REQUIRED' => !$field->required ? '->notRequired()' : null,
			]);
 		}

		foreach($enums as $enum)
		{
			$uses[] = "use App\Enums\\".$enum->enum.';';

			$table_fields[] = FileManipulation::getStubContents('admin_resource_table_column', [
				'TITLE'  => Str::replace('_', ' ', ucfirst($enum->field)),
				'NAME'  => $enum->field,
				'MODIFIERS' => '->enum('.$enum->enum.'::class)',
			]);

			$form_fields[] = FileManipulation::getStubContents('admin_resource_form_field', [
				'TITLE'  => Str::replace('_', ' ', ucfirst($enum->field)),
				'NAME'  => $enum->field,
				'MODIFIERS' => '->enum('.$enum->enum.'::cases())',
				'NOT_REQUIRED' => null
			]);
		}

		$actions = join(', ', array_map(function(string $action) {
			return "'$action'";
		}, $actions));

		usort($uses, function($a, $b) {
            return strlen($a) - strlen($b);
        });

		FileManipulation::putContentFile('admin_resource', 'app/AdminPanel/'.$name.'Resource.php', [
			'CLASS' => $name.'Resource',
			'TITLE' => $title ?? $name,
			'USES' => join("\n", array_unique($uses)),
			'ACTIONS' => $actions,
			'TABLE_FIELDS' => join("\n\n", $table_fields),
			'FORM_FIELDS' => join("\n\n", $form_fields)
		]);

		$this->info('Admin Resource created successfully');
	}


	private function _createModel(string $name_model, array $relations, array $fields, array $enums)
	{
		$uses = [
			'use Illuminate\Database\Eloquent\Model;',
			'use Illuminate\Database\Eloquent\Factories\HasFactory;'
		];

		$casts = $relationships = [];

		foreach($fields as $field)
		{
			if($field->type == 'date')
			{
				$casts[] = str_repeat("\t", 2)."'".$field->name."' => 'datetime',";
			}
		}

		foreach($enums as $enum)
		{
			$uses[] = "use App\Enums\\".$enum->enum.';';
			
			$casts[] = str_repeat("\t", 2)."'".$enum->field."' => ".$enum->enum."::class,";
		}

		foreach($relations as $relation)
		{
			$uses[] = "use App\Models\\".$relation->model.';';
			
			$relationships[] = FileManipulation::getStubContents('relationship', [
				'FIELD' => str_replace('_id', '', $relation->field),
				'TYPE' => $relation->type,
				'MODEL' => $relation->model
			]);
		}

		usort($uses, function($a, $b) {
            return strlen($a) - strlen($b);
        });

		FileManipulation::putContentFile('model', 'app/Models/'.$name_model.'.php', [
			'CLASS' => $name_model,
			'USES' => join("\n", array_unique($uses)),
			'RELATIONSHIPS' => join("\n", $relationships),
			'CASTS' => ($casts) ? FileManipulation::getStubContents('casts', [
				'CASTS' => join("\n", $casts),
			]) : null
		]);

		$this->info('Model created successfully');
	}

	private function _createFactory(string $name_model, array $fields, array $relations, array $enums)
	{
		$uses = [
			'use Illuminate\Database\Eloquent\Factories\Factory;',
		];

		$fields_factory = [];
        
		foreach($fields as $field)
        {
			switch($field->type)
			{
				case 'text':
					$faker_field = 'fake()->sentence(10)';
					break;
				
				case 'date':
					$faker_field = "fake()->date('Y-m-d')";
					break;
								
				case 'decimal':
					$faker_field = 'fake()->randomFloat(2, 0, 10000)';
					break;
								
				case 'integer':
				case 'tinyInteger':
				case 'bigInteger':
					$faker_field = 'fake()->randomDigit()';
					break;

				case 'string':
				default: 
					$faker_field = 'fake()->word()';
			}

            $fields_factory[$field->name] = $faker_field;
        }

		foreach($enums as $enum)
        {
			$uses[] = "use App\Enums\\".$enum->enum.';';

			$faker_field = 'fake()->randomElement('.$enum->enum.'::cases())';

            $fields_factory[$enum->field] = $faker_field;
        }

		foreach($relations as $relation)
        {
			$uses[] = "use App\Models\\".$relation->model.';';

            $fields_factory[$relation->field] = $relation->model.'::inRandomOrder()->limit(1)->first()->id ?? '.$relation->model.'::factory()->create()->id';
        }

		foreach($fields_factory as $field => &$faker)
		{
			$faker = str_repeat("\t",3)."'".$field."' => ".$faker.",";
		}

		usort($uses, function($a, $b) {
            return strlen($a) - strlen($b);
        });

		FileManipulation::putContentFile('factory', 'database/factories/'.$name_model.'Factory.php', [
			'CLASS' => $name_model,
			'USES' => join("\n", array_unique($uses)),
			'FIELDS' => join("\n", $fields_factory),
		]);

		$this->info('Factory created successfully');
	}

	private function _createSeeder(string $name_model)
	{
		$uses = [
			"use App\Models\\".$name_model.';',
			'use Illuminate\Database\Seeder;'
		];

		usort($uses, function($a, $b) {
            return strlen($a) - strlen($b);
        });

		FileManipulation::putContentFile('seeder', 'database/seeders/'.$name_model.'Seeder.php', [
			'CLASS' => $name_model,
			'USES' => join("\n", array_unique($uses)),
		]);

		$this->info('Seeder created successfully');
	}

	private function _createMigration(string $resource_name, array $fields, array $relations, array $enums)
	{
		$table = Utils::nameTable($resource_name);

		$name = 'create_'.$table.'_table';

		$name_file = date('Y_m_d_His').'_'.$name.'.php';

		$dir = app_path('../database/migrations');

        $migration_exists =  glob($dir.'/*'.$name.'.php');

		foreach($migration_exists as $file)
		{
			$name_file_existing = explode('/', $file);

            $name_file = end($name_file_existing);
		}

		$fields_migration = [];
        
		foreach($fields as $field)
        {
            $fields_migration[] = FileManipulation::getStubContents('field_migration', [
				'TYPE'  => $field->type,
				'COLUMN'  => $field->name,
				'NULLABLE' => ($field->required) ? null : '->nullable()',
				'REFERENCES' => null,
			]);
        }

		foreach($enums as $enum)
        {
            $fields_migration[] = FileManipulation::getStubContents('field_migration', [
				'TYPE' => 'tinyInteger',
				'COLUMN'  => $enum->field,
				'NULLABLE' => null,
				'REFERENCES' => null,
			]);
        }

		foreach($relations as $relation)
        {
            $fields_migration[] = FileManipulation::getStubContents('field_migration', [
				'TYPE' => 'foreignId',
				'COLUMN'  => $relation->field,
				'NULLABLE' => null,
				'REFERENCES' => "->references('id')->on('".Utils::nameTable($relation->model)."')",
			]);
        }

		FileManipulation::putContentFile('migration', 'database/migrations/'.$name_file, [
			'TABLE' => $table,
            'FIELDS' => join("\n", $fields_migration),
		]);

		$this->info('Migration created successfully');
	}

	private function _createEnums(array $enums)
	{
		$folder = 'app/Enums';

		if(!File::exists($folder))
		{
            File::makeDirectory($folder);
		}

		foreach($enums as $enum)
		{
			FileManipulation::putContentFile('enum', $folder.'/'.$enum->enum.'.php', [
				'CLASS' => $enum->enum,
			]);
		}

		$this->info('Enums created successfully');
	}
}