<?php

namespace S4mpp\Laragenius\Enums;

use S4mpp\Laragenius\Fields\Integer;
use S4mpp\Laragenius\Fields\Varchar;
use S4mpp\Laragenius\Fields\Datetime;

enum ColumnType: string
{
    case Integer = 'integer';
    case Varchar = 'varchar';
    case Datetime = 'datetime';

    public function class(): string
    {
        return match ($this) {
            self::Integer => Integer::class,
            self::Varchar => Varchar::class,
            self::Datetime => Datetime::class
        };
    }
}
