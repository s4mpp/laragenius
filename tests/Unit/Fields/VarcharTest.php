<?php

namespace S4mpp\Laragenius\Tests\Unit\Fields;

use S4mpp\Laragenius\Fields\Varchar;
use S4mpp\Laragenius\Tests\TestCase;

class VarcharTest extends TestCase
{
    public static function nameFieldVarchar()
    {
        return [
            'email' => ['email', 'stubs/factory/fakers/email'],
            'name' => ['name', 'stubs/factory/fakers/name'],
            'password' => ['password', 'stubs/factory/fakers/password'],
            'token' => ['token', 'stubs/factory/fakers/token'],
            'phone' => ['phone', 'stubs/factory/fakers/phone'],
        ];
    }

    /**
     * @dataProvider nameFieldVarchar
     */
    public function test_get_faker(string $name_field, string $file_name): void
    {
        $field = new Varchar();

        $faker = $field->getFaker($name_field);

        $this->assertEquals($file_name, $faker->getNameFile());
    }
}
