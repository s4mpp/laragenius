<?php

namespace S4mpp\Laragenius\Enums;

enum ColumnType: string
{
    //TODO Type boolean

    case Integer = 'integer';
    case BigInteger = 'bigint';
    case TinyInt = 'tinyint';
    case Int = 'int';
    case Decimal = 'decimal';
    case Numeric = 'numeric';
    case Varchar = 'varchar';
    case Datetime = 'datetime';
    case Float = 'float';
    case Timestamp = 'timestamp';
    case Date = 'date';
    case Text = 'text';
    case Char = 'char';
    case Json = 'json';

    public function faker(): string
    {
        return match ($this) {
            self::Integer => 'randomInteger',
            self::BigInteger => 'randomInteger',
            self::Int => 'randomInteger',
            self::TinyInt => 'randomInteger',
            self::Numeric => 'randomFloat',
            self::Decimal => 'randomFloat',
            self::Float => 'randomFloat',
            self::Varchar => 'word',
            self::Char => 'word',
            self::Datetime => 'dateTime',
            self::Timestamp => 'dateTime',
            self::Date => 'date',
            self::Text => 'text',

            default => 'word',
        };
    }

    public function cast(): ?string
    {
        return match ($this) {
            self::Datetime => 'datetime',
            self::Timestamp => 'datetime',
            self::Date => 'date',

            default => null,
        };
    }
}
