<?php

namespace S4mpp\Laragenius\Schema;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\ColumnType;
use S4mpp\Laragenius\Enums\RelationshipType;

class Table
{
    /** @var array<Column> */
    private array $columns = [];

    private const TABLES_EXCLUDED = [
        'failed_jobs',
        'migrations',
        'password_reset_tokens',
        'model_has_permissions', 'model_has_roles', 'role_has_permissions', 'roles',
        'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring',
    ];

    public function __construct(private string $name)
    {
        if (! Schema::hasTable($this->name)) {
            throw new \Exception('Table '.$this->name.' not found');
        }
    }

    public static function toModelName(string $name): string
    {
        return Str::studly(Str::singular($name));
    }

    public function getModelName(): string
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
    public function getColumns(bool $filter = true): array
    {
        if ($filter) {
            return array_filter($this->columns, fn ($column) => ! (in_array($column->getName(), ['id', 'created_at', 'updated_at', 'deleted_at'])));
        }

        return $this->columns;
    }

    // TODO call in constructor
    public function loadColumns(): self
    {
        $columns = Schema::getColumns($this->name);

        foreach ($columns as $column) {
            $this->columns[$column['name']] = (new Column($column['name'], ColumnType::tryFrom($column['type_name'])))->setNullable($column['nullable']);
        }

        return $this;
    }

    public function loadUniqueIndexes(): self
    {
        $keys = Schema::getIndexes($this->name);

        array_map(function ($key): void {
            if (! $key['unique']) {
                return;
            }

            foreach ($key['columns'] as $column) {
                $this->getColumn($column)?->setUnique(true);
            }
        }, $keys);

        return $this;
    }

    public function loadRelationships(): self
    {
        $tables = array_filter(Schema::getTableListing(), fn (string $table) => ! in_array($table, self::TABLES_EXCLUDED));

        array_map(function ($table_name): void {
            $foreign_keys = Schema::getForeignKeys($table_name);

            if ($table_name == $this->name) {
                $this->setBelongsToRelationships($foreign_keys);

                return;
            }

            //TODO add foreign column name
            $this->setHasManyRelationship($foreign_keys, $table_name);
        }, $tables);

        return $this;
    }

    /**
     * @param  array<array<array<string>|string>>  $foreign_keys
     */
    private function setBelongsToRelationships(array $foreign_keys): void
    {
        foreach ($foreign_keys as $foreign_key) {
            /** @var array<string> */
            $columns = $foreign_key['columns'];
            foreach ($columns as $column) {
                /** @var string */
                $foreign_table = $foreign_key['foreign_table'];
                $this->getColumn($column)?->addRelationship(new Relationship($foreign_table, RelationshipType::BelongsTo));
            }
        }
    }

    /**
     * @param  array<array<array<string>|string>>  $foreign_keys
     */
    private function setHasManyRelationship(array $foreign_keys, string $table_name): void
    {
        foreach ($foreign_keys as $foreign_key) {
            /** @var array<string> */
            $foreign_columns = $foreign_key['foreign_columns'];

            foreach ($foreign_columns as $foreign_column) {
                if ($foreign_key['foreign_table'] != $this->name) {
                    continue;
                }

                $this->getColumn($foreign_column)?->addRelationship(new Relationship($table_name, RelationshipType::HasMany));
            }
        }
    }

    private function getColumn(string $column): ?Column
    {
        if (! isset($this->columns[$column])) {
            return null;
        }

        return $this->columns[$column];
    }
}
