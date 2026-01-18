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

    private static string $output_path;

    public static function addGenerator(string $generator): void
    {
        if (in_array($generator, self::$generators)) {
            return;
        }

        self::$generators[] = $generator;
    }

    public static function flushGenerators(): void
    {
        self::$generators = [];
    }

    public static function setOutputPath(string $path): void
    {
        self::$output_path = $path;
    }

    public static function getOutputPath(): string
    {
        if (isset(self::$output_path)) {
            return self::$output_path;
        }

        return base_path();
    }

    /**
     * @return array<string>
     */
    public static function getGenerators(): array
    {
        $built_in_generators = [
            Model::class,
            Factory::class,
            Seeder::class,
        ];

        return array_merge($built_in_generators, self::$generators);
    }
}
