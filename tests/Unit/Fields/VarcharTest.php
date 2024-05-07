<?php

namespace S4mpp\Laragenius\Tests\Unit\Fields;

use S4mpp\Laragenius\Fields\Varchar;
use S4mpp\Laragenius\Tests\TestCase;

class VarcharTest extends TestCase
{
    public static function nameFieldVarchar()
    {
        return [
            'email' => ['email', 'email()'],
            'name' => ['name', 'firstName()'],
            'password' => ['password', 'password'],
            'token' => ['token', 'sha1()'],
            'phone' => ['phone', 'phoneNumber()'],
        ];
    }

    /**
     * @dataProvider nameFieldVarchar
     */
    public function test_get_faker(string $name_field, string $file_name): void
    {
        $field = new Varchar();

        $faker = $field->getFaker($name_field);

        $this->assertStringContainsString($file_name, (string) $faker);
    }
}
