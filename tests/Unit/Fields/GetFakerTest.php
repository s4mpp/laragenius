<?php

namespace S4mpp\Laragenius\Tests\Unit\Fields;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Fields\Date;
use S4mpp\Laragenius\Fields\Text;
use S4mpp\Laragenius\Fields\Decimal;
use S4mpp\Laragenius\Fields\Integer;
use S4mpp\Laragenius\Fields\TinyInt;
use S4mpp\Laragenius\Fields\Varchar;
use S4mpp\Laragenius\Tests\TestCase;
use S4mpp\Laragenius\Fields\Datetime;

class GetFakerTest extends TestCase
{
    public static function fieldsWithFakerProvider()
    {
        return [
            'integer' => [Integer::class, 'stubs/factory/fakers/random-number'],
            'decimal' => [Decimal::class, 'stubs/factory/fakers/decimal'],
            'varchar' => [Varchar::class, 'stubs/factory/fakers/word'],
            'datetime' => [Datetime::class, 'stubs/factory/fakers/date'],
            'date' => [Date::class, 'stubs/factory/fakers/date'],
            'tinyint' => [TinyInt::class, 'stubs/factory/fakers/random-digit'],
            'text' => [Text::class, 'stubs/factory/fakers/text'],
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
        $this->assertEquals($file_name, $faker->getNameFile());
    }
}
