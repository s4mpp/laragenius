<?php

namespace S4mpp\Laragenius\Tests\Unit\Fields;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Fields\Date;
use S4mpp\Laragenius\Fields\Text;
use S4mpp\Laragenius\Fields\Decimal;
use S4mpp\Laragenius\Fields\Integer;
use S4mpp\Laragenius\Fields\Varchar;
use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Fields\Datetime;

class GetFakerTest extends TestCase
{
    public static function fieldsWithFakerProvider()
    {
        return [
            'integer' => [Integer::class, 'randomNumber()'],
            'decimal' => [Decimal::class, 'randomFloat()'],
            'varchar' => [Varchar::class, 'word()'],
            'datetime' => [Datetime::class, "date('Y-m-d H:i:s')"],
            'date' => [Date::class, "fake()->date('Y-m-d')"],
            'text' => [Text::class, 'text()'],
        ];
    }

    /**
     * @dataProvider fieldsWithFakerProvider
     */
    public function test_get_faker(string $class, string $file_name): void
    {
        $field = new $class();

        $faker = $field->getFaker('id');
        $this->assertInstanceOf(Stub::class, $faker);
        $this->assertStringContainsString($file_name, (string) $faker);
    }
}
