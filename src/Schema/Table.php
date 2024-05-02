<?php

namespace S4mpp\Laragenius\Schema;

use Illuminate\Support\Facades\Schema;
use S4mpp\Laragenius\Enums\ColumnType;

class Table
{
    /** @var array<Column> */
    private array $columns;
    
    /** @var array<string> */
    private array $uniques = [];

    public function __construct(private string $name)
    {
        $this->setColumnsUnique();

        $this->setColumns();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setColumnsUnique(): void
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

    public function setColumns(): void
    {
        $columns = Schema::getColumns($this->name);

        $this->columns = array_map(function ($c) {
            $column = new Column($c['name'], ColumnType::from($c['type']));

            $column->setUnique(in_array($c['name'], $this->uniques));
            $column->setNullable($c['nullable']);

            return $column;
        }, $columns);
    }

    /**	 *
     * @return array<Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
