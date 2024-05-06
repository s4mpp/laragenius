<?php

namespace S4mpp\Laragenius\Tests\Unit\Fields;

use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Enums\RelationshipType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelationshipTypeTest extends TestCase
{
    public function test_name_method(): void
    {
        $enum = RelationshipType::HasMany;

        $this->assertEquals('newUsers', $enum->nameMethod('new_users'));
        $this->assertEquals('oldUsers', $enum->nameMethod('old_users'));
    }

    public function test_stub(): void
    {
        $this->assertEquals('has_many_relationship', RelationshipType::HasMany->stub());
    }

    public function test_class_relation_laravel(): void
    {
        $this->assertEquals(BelongsTo::class, RelationshipType::BelongsTo->classRelationLaravel());
    }
}
