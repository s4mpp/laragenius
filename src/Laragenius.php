<?php

namespace S4mpp\Laragenius;

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
        return self::$generators;
    }
}
