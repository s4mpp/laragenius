<?php

namespace S4mpp\Laragenius;

use S4mpp\Laragenius\Generators\Model;
use S4mpp\Laragenius\Generators\Seeder;
use S4mpp\Laragenius\Generators\Factory;

final class Laragenius
{
    /**
     * @var array<string>
     */
    private static array $generators = [];

    public static function addGenerator(string $generator): void
    {
        self::$generators[] = $generator;
    }

    /**
     * @return array<string>
     */
    public static function getGenerators(): array
    {
        return array_merge([Model::class, Factory::class, Seeder::class], self::$generators);
    }

    public static function flushGenerators(): void
    {
        self::$generators = [];

        return;
    }
}
