<?php

namespace S4mpp\Laragenius\Schema;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\ColumnType;
use S4mpp\Laragenius\Enums\RelationshipType;

final class Table
{
    public function __construct(private string $name) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getStudlyName(): string
    {
        return Str::studly(Str::singular($this->name));
    }

    /**	 *
     * @return array<Column>
     */
    public function getColumns(): array
    {
        $columns = Schema::getColumns($this->name);

        //------------------
        //TODO  move to $this->getUniqueKeys()
        $keys = Schema::getIndexes($this->name);

        $uniques = [];

        array_map(function (array $key) use (&$uniques): void {
            if (! $key['unique']) {
                return;
            }

            foreach ($key['columns'] as $column) {
                $uniques[] = $column;
            }
        }, $keys);

        //------------------
        //TODO  move to $this->getRelationships()
        $relationships = [];

        $tables = Schema::getTableListing(schemaQualified: false);

        array_map(function ($table_name) use (&$relationships): void {
            $foreign_keys = Schema::getForeignKeys($table_name);

            foreach ($foreign_keys as $foreign_key) {

                if ($table_name == $this->name) {
                    $column = $foreign_key['columns'][0];

                    $relationships[$column][] = new Relationship(new Table($foreign_key['foreign_table']), RelationshipType::BelongsTo);
                }

                if ($foreign_key['foreign_table'] == $this->name) {
                    $relationships['id'][] = new Relationship(new Table($table_name), RelationshipType::HasMany);
                }
            }
        }, $tables);

        //------------------

        $columns = array_map(function (array $column) use ($uniques, $relationships): Column {

            $type = ColumnType::from($column['type_name']);

            $is_unique = in_array($column['name'], $uniques);

            $instance = new Column($column['name'], $column['nullable'], $is_unique, $type);

            foreach ($relationships[$column['name']] ?? [] as $relationship) {
                $instance->addRelationship($relationship);
            }

            return $instance;
        }, $columns);

        return $columns;
    }
}
