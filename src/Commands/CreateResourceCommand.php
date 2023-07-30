<?php
namespace Samuelpacheco\Laragenius\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Samuelpacheco\Laragenius\Utils;
use Illuminate\Support\Facades\File;
use Samuelpacheco\Laragenius\FileManipulation;

class CreateResourceCommand extends Command
{
	protected $signature = 'lg:create-resource
                            {resource_name : Name of resource}';

	protected $description = 'Generate files of new resource';

	public function handle()
    {
        $resource_name = Str::lower($this->argument('resource_name'));

		$file_config = 'laragenius'.DIRECTORY_SEPARATOR.$resource_name.'.json';

		if(!file_exists($file_config))
		{
			return $this->error('Config file '.$resource_name.'.json not found');
		}

		$config = json_decode(file_get_contents($file_config));

		$this->_createModel($config->name, $config->relations, $config->enums);
		
		$this->_createFactory($config->name, $config->fields, $config->relations, $config->enums);
		
		$this->_createSeeder($config->name);
		
		$this->_createMigration($config->name, $config->fields, $config->relations, $config->enums);
		
		$this->_createEnums($config->enums);

    }

	private function _createModel(string $name_model, array $relations, array $enums)
	{
		$uses = [
			'use Illuminate\Database\Eloquent\Model;',
			'use Illuminate\Database\Eloquent\Factories\HasFactory;'
		];

		$casts = $relationships = [];

		foreach($enums as $enum)
		{
			$uses[] = "use App\Enums\\".$enum->enum.';';
			
			$casts[] = "'".$enum->field."' => ".$enum->enum."::class,";
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
			'USES' => join("\n", $uses),
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
				case 'string':
					$faker_field = 'fake()->word()';
					break;
				
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
					$faker_field = 'fake()->randomDigit()';
					break;
				
				case 'tinyInteger':
					$faker_field = 'fake()->randomDigit()';
					break;

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
			'USES' => join("\n", $uses),
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
			'USES' => join("\n", $uses),
		]);

		$this->info('Seeder created successfully');
	}

	private function _createMigration(string $resource_name, array $fields, array $relations, array $enums)
	{
		$table = Utils::nameTable($resource_name);

		$name = 'create_'.$table.'_table';

		$class = Str::ucfirst(Str::camel($name));

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

		FileManipulation::putContentFile('migration', 'database/migrations/'.date('Y_m_d_His').'_'.$name.'.php', [
			'TABLE' => $table,
			'CLASS' => $class,
            'FIELDS' => join("\n", $fields_migration),
		]);

		$this->info('migration created successfully');
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