<?php

namespace S4mpp\Laragenius;

use Illuminate\Support\Str;

use function Laravel\Prompts\info;
use Illuminate\Support\Facades\File;
use S4mpp\Laragenius\FileManipulation;

class Resource
{
	public function __construct(
		private string $name,
		private string $title,
		private array $fields,
		private array $actions,
		private array $relations,
		private array $enums)
	{}

	public function createModel()
	{
		$uses = [
			'use Illuminate\Database\Eloquent\Model;',
			'use Illuminate\Database\Eloquent\Factories\HasFactory;'
		];

		$casts = $relationships = [];

		foreach($this->fields as $field)
		{
			if($field->type == 'date' || $field->type == 'datetime')
			{
				$casts[] = str_repeat("\t", 2)."'".$field->name."' => 'datetime',";
			}
		}

		foreach($this->enums as $enum)
		{
			$uses[] = "use App\Enums\\".$enum->enum.';';
			
			$casts[] = str_repeat("\t", 2)."'".$enum->field."' => ".$enum->enum."::class,";
		}

		foreach($this->relations as $relation)
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

		FileManipulation::putContentFile('model', 'app/Models/'.$this->name.'.php', [
			'CLASS' => $this->name,
			'USES' => join("\n", array_unique($uses)),
			'RELATIONSHIPS' => join("\n", $relationships),
			'CASTS' => ($casts) ? FileManipulation::getStubContents('casts', [
				'CASTS' => join("\n", $casts),
			]) : null
		]);

		info('Model created successfully');
	}

	public function createFactory()
	{
		$uses = [
			'use Illuminate\Database\Eloquent\Factories\Factory;',
		];

		$fields_factory = [];
        
		foreach($this->fields as $field)
        {
			$faker_field = 'fake()';

			if(isset($field->unique) && $field->unique)
			{
				$faker_field .= '->unique()';
			}

			switch($field->type)
			{
				case 'text':
					$faker_field .= '->sentence(10)';
					break;
				
				case 'date':
					$faker_field .= "->date('Y-m-d')";
					break;
				
				case 'datetime':
					$faker_field .= "->date('Y-m-d H:i:s')";
					break;
								
				case 'decimal':
					$faker_field .= '->randomFloat(2, 0, 10000)';
					break;
				
				case 'boolean':
					$faker_field .= '->boolean()';
					break;
								
				case 'integer':
				case 'tinyInteger':
				case 'bigInteger':
					$faker_field .= '->randomDigit()';
					break;

				case 'string':
				default: 
					$faker_field .= '->word()';
			}

            $fields_factory[$field->name] = $faker_field;
        }

		foreach($this->enums as $enum)
        {
			$uses[] = "use App\Enums\\".$enum->enum.';';

			$faker_field = 'fake()->randomElement('.$enum->enum.'::cases())';

            $fields_factory[$enum->field] = $faker_field;
        }

		foreach($this->relations as $relation)
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

		FileManipulation::putContentFile('factory', 'database/factories/'.$this->name.'Factory.php', [
			'CLASS' => $this->name,
			'USES' => join("\n", array_unique($uses)),
			'FIELDS' => join("\n", $fields_factory),
		]);

		info('Factory created successfully');
	}

	public function createSeeder()
	{
		$uses = [
			"use App\Models\\".$this->name.';',
			'use Illuminate\Database\Seeder;'
		];

		usort($uses, function($a, $b) {
            return strlen($a) - strlen($b);
        });

		FileManipulation::putContentFile('seeder', 'database/seeders/'.$this->name.'Seeder.php', [
			'CLASS' => $this->name,
			'USES' => join("\n", array_unique($uses)),
		]);

		info('Seeder created successfully');
	}

	public function createMigration()
	{
		$table = Utils::nameTable($this->name);

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
        
		foreach($this->fields as $field)
        {
            $fields_migration[] = FileManipulation::getStubContents('field_migration', [
				'TYPE'  => $field->type,
				'COLUMN'  => $field->name,
				'NULLABLE' => ($field->required) ? null : '->nullable()',
				'UNIQUE' => (isset($field->unique) && $field->unique) ? '->unique()' : null,
				'REFERENCES' => null,
			]);
        }

		foreach($this->enums as $enum)
        {
            $fields_migration[] = FileManipulation::getStubContents('field_migration', [
				'TYPE' => 'tinyInteger',
				'COLUMN'  => $enum->field,
				'NULLABLE' => null,
				'UNIQUE' => null,
				'REFERENCES' => null,
			]);
        }

		foreach($this->relations as $relation)
        {
            $fields_migration[] = FileManipulation::getStubContents('field_migration', [
				'TYPE' => 'foreignId',
				'COLUMN'  => $relation->field,
				'NULLABLE' => null,
				'UNIQUE' => null,
				'REFERENCES' => "->references('id')->on('".Utils::nameTable($relation->model)."')",
			]);
        }

		FileManipulation::putContentFile('migration', 'database/migrations/'.$name_file, [
			'TABLE' => $table,
            'FIELDS' => join("\n", $fields_migration),
		]);

		info('Migration created successfully');
	}

	public function createEnums()
	{
		$folder = 'app/Enums';

		if(!File::exists($folder))
		{
            File::makeDirectory($folder);
		}

		foreach($this->enums as $enum)
		{
			FileManipulation::putContentFile('enum', $folder.'/'.$enum->enum.'.php', [
				'CLASS' => $enum->enum,
			]);
		}

		info('Enums created successfully');
	}

	public function createAdminResource()
	{
		$uses = [
			'use S4mpp\AdminPanel\Elements\Column;',
			'use S4mpp\AdminPanel\Resources\Resource;',
		];

		$table_fields = $form_fields = $read_fields = [];

		foreach($this->relations as $relation)
		{
			$uses[] = "use App\Models\\".$relation->model.';';

			$title = ucfirst($relation->title ?? Str::replace(['_id', '_'], ['', ' '], $relation->field));

			$table_fields[] = FileManipulation::getStubContents('admin_resource_table_column', [
				'TITLE'  => $title,
				'NAME'  => Str::replace('_id', '', $relation->field),
				'MODIFIERS' => "->relation('".($relation->fk_label ?? 'id')."')",
			]);

			$form_fields[] = FileManipulation::getStubContents('admin_resource_form_field', [
				'TITLE'  => $title,
				'NAME'  => $relation->field,
				'MODIFIERS' => '->relation('.$relation->model."::all(), '".($relation->fk_label ?? 'id')."')",
				'NOT_REQUIRED' => null
			]);

			$read_fields[] = FileManipulation::getStubContents('admin_resource_read_field', [
				'TITLE'  => $title,
				'NAME'  => Str::replace('_id', '', $relation->field),
				'MODIFIERS' => "->relation('".($relation->fk_label ?? 'id')."')",
			]);
		}

		foreach($this->fields as $field)
		{
			$field_modifiers = $table_modifiers = $read_modifiers = [];

			switch($field->type)
			{
				case 'date':
					$field_modifiers[] = '->date()';
					$table_modifiers[] = "->datetime('d/m/Y')";
					$read_modifiers[] = "->datetime('d/m/Y')";
					break;
				
				case 'boolean':
					$field_modifiers[] = '->boolean()';
					$table_modifiers[] = "->boolean()->align('center')";
					$read_modifiers[] = "->boolean()";
					break;
					
				case 'datetime':
					$field_modifiers[] = '->date()';
					$table_modifiers[] = "->datetime('d/m/Y H:i')";
					$read_modifiers[] = "->datetime('d/m/Y H:i')";
					break;
				
				case 'decimal':
					$field_modifiers[] = "->decimal()->min(0.1)";
					$table_modifiers[] = "->align('right')";
					break;
				
				case 'integer':
				case 'tinyInteger':
				case 'bigInteger':
					$field_modifiers[] = "->integer()->min(1)";
					$table_modifiers[] = "->align('right')";
					break;
				
				case 'text':
					$field_modifiers[] = '->textarea(4)';
					break;
			}

			if(isset($field->unique) && $field->unique)
			{
				$field_modifiers[] = '->unique()';
			}

			$title = ucfirst($field->title ?? Str::replace('_', ' ', $field->name));

			$table_fields[] = FileManipulation::getStubContents('admin_resource_table_column', [
				'TITLE'  => $title,
				'NAME'  => $field->name,
				'MODIFIERS' => join('', $table_modifiers),
			]);
			
			$form_fields[] = FileManipulation::getStubContents('admin_resource_form_field', [
				'TITLE'  => $title,
				'NAME'  => $field->name,
				'MODIFIERS' => join('', $field_modifiers),
				'NOT_REQUIRED' => !$field->required ? '->notRequired()' : null,
			]);

			$read_fields[] = FileManipulation::getStubContents('admin_resource_read_field', [
				'TITLE'  => $title,
				'NAME'  => $field->name,
				'MODIFIERS' => join('', $read_modifiers),
			]);
 		}

		foreach($this->enums as $enum)
		{
			$uses[] = "use App\Enums\\".$enum->enum.';';

			$title = ucfirst($enum->title ?? Str::replace('_', ' ', $enum->field));

			$table_fields[] = FileManipulation::getStubContents('admin_resource_table_column', [
				'TITLE'  => $title,
				'NAME'  => $enum->field,
				'MODIFIERS' => '->enum()',
			]);

			$form_fields[] = FileManipulation::getStubContents('admin_resource_form_field', [
				'TITLE'  => $title,
				'NAME'  => $enum->field,
				'MODIFIERS' => '->enum('.$enum->enum.'::cases())',
				'NOT_REQUIRED' => null
			]);

			$read_fields[] = FileManipulation::getStubContents('admin_resource_read_field', [
				'TITLE'  => $title,
				'NAME'  => $enum->field,
				'MODIFIERS' => '->enum()'
			]);
		}

		$actions = join(', ', array_map(function(string $action) {
			return "'$action'";
		}, $this->actions));


		if(in_array('create', $this->actions) || in_array('update', $this->actions))
		{
			$uses[] = 'use S4mpp\AdminPanel\Elements\Card;';
			$uses[] = 'use S4mpp\AdminPanel\Elements\Field;';
			
			$get_form = FileManipulation::getStubContents('admin_resource_get_form', [
				'FORM_FIELDS' => join("\n\n", $form_fields),
			]);
		}
		
		if(in_array('read', $this->actions))
		{
			$uses[] = 'use S4mpp\AdminPanel\Elements\ItemView;';

			$get_read = FileManipulation::getStubContents('admin_resource_get_read', [
				'READ_FIELDS' => join("\n\n", $read_fields),
			]);
		}

		usort($uses, function($a, $b) {
            return strlen($a) - strlen($b);
        });

		FileManipulation::putContentFile('admin_resource', 'app/AdminPanel/'.$this->name.'Resource.php', [
			'CLASS' => $this->name.'Resource',
			'TITLE' => Str::plural($this->title ?? $this->name),
			'USES' => join("\n", array_unique($uses)),
			'ACTIONS' => $actions,
			'TABLE_FIELDS' => join("\n\n", $table_fields),
			'GET_FORM' => $get_read ?? null,
			'GET_READ' => $get_form ?? null,
		]);

		info('Admin Resource created successfully');
	}
}