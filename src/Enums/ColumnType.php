<?php

namespace S4mpp\Laragenius\Enums;

use S4mpp\Laragenius\Fields\Date;
use S4mpp\Laragenius\Fields\Text;
use S4mpp\Laragenius\Fields\Decimal;
use S4mpp\Laragenius\Fields\Integer;
use S4mpp\Laragenius\Fields\TinyInt;
use S4mpp\Laragenius\Fields\Varchar;
use S4mpp\Laragenius\Fields\Datetime;

enum ColumnType: string
{
    //TODO Type boolean

    case Integer = 'integer';
    case Decimal = 'numeric';
    case Varchar = 'varchar';
    case Datetime = 'datetime';
    case Date = 'date';
    case Text = 'text';

    public function class(): string
    {
        return match ($this) {
            self::Integer => Integer::class,
            self::Decimal => Decimal::class,
            self::Varchar => Varchar::class,
            self::Datetime => Datetime::class,
            self::Date => Date::class,
            self::Text => Text::class
        };
    }

    public function cast(): ?string
    {
        return match ($this) {
            self::Datetime => 'datetime',
            self::Date => 'date',

            default => null,
        };
    }
}
