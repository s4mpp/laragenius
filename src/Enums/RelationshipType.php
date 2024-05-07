<?php

namespace S4mpp\Laragenius\Enums;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

enum RelationshipType
{
    case HasMany;
    case BelongsTo;

    public function nameMethod(string $table_name): string
    {
        return match ($this) {
            self::HasMany => Str::camel($table_name),
            self::BelongsTo => Str::camel(Str::singular($table_name)),
        };
    }

    public function stub(): string
    {
        return match ($this) {
            self::HasMany => 'has_many_relationship',
            self::BelongsTo => 'belongs_to_relationship',
        };
    }

    public function classRelationLaravel(): string
    {
        return match ($this) {
            self::HasMany => HasMany::class,
            self::BelongsTo => BelongsTo::class,
        };
    }
}
