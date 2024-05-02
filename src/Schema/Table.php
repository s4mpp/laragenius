<?php

namespace S4mpp\Laragenius\Schema;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\ColumnType;
use S4mpp\Laragenius\Enums\RelationshipType;

class Table
{
    /** @var array<Column> */
    private array $columns;

    /** @var array<string> */
    private array $uniques = [];

    /** @var array<Relationship> */
    private array $relationships = [];

    private const TABLES_EXCLUDED = [
        'failed_jobs',
        'migrations',
        'password_reset_tokens',
        'model_has_permissions', 'model_has_roles', 'role_has_permissions', 'roles',
        'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring',
    ];

    private const COLUMNS_EXCLUDED = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];


    public function __construct(private string $name)
    {
    }
    
    public static function toModelName(string $name)
    {
        return Str::studly(Str::singular($name));
    }

    public function getModelName()
    {
        return self::toModelName($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**	 *
     * @return array<Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**	 *
     * @return array<Relationship>
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    public function loadUniqueColumns(): void
    {
        $keys = Schema::getIndexes($this->name);

        array_map(function ($key): void {
            if (! $key['unique']) {
                return;
            }

            foreach ($key['columns'] as $column) {
                $this->uniques[] = $column;
            }
        }, $keys);
    }

    public function loadColumns(): void
    {
        $columns = Schema::getColumns($this->name);

        $columns = array_filter($columns, function ($c) {

            if(in_array($c['name'], self::COLUMNS_EXCLUDED)) {
                return false;
            }

            return true;
        });

        $this->columns = array_map(function ($c) {
            $column = new Column($c['name'], ColumnType::from($c['type']));

            $column->setUnique(in_array($c['name'], $this->uniques));
            $column->setNullable($c['nullable']);

            return $column;
        }, $columns);
    }

    public function loadRelationships(): void
    {
        $tables = array_filter(Schema::getTableListing(), fn (string $table) => ! in_array($table, self::TABLES_EXCLUDED));

        array_map(function ($table_name): void {
            $foreign_keys = Schema::getForeignKeys($table_name);

            if ($table_name == $this->name) {
                $this->setBelongsToRelationships($foreign_keys);

                return;
            }

            $this->setHasManyRelationshiop($foreign_keys, $table_name);
        }, $tables);
    }

    /**
     * @param  array<array<string>>  $foreign_keys
     */
    private function setBelongsToRelationships(array $foreign_keys): void
    {
        foreach ($foreign_keys as $table_name) {
            $this->relationships[] = new Relationship($table_name['foreign_table'], RelationshipType::BelongsTo);
        }
    }

    /**
     * @param  array<array<string>>  $foreign_keys
     */
    private function setHasManyRelationshiop(array $foreign_keys, string $table_name): void
    {
        foreach ($foreign_keys as $foreign_key) {
            if ($foreign_key['foreign_table'] != $this->name) {
                continue;
            }

            $this->relationships[] = new Relationship($table_name, RelationshipType::HasMany);
        }
    }
}
