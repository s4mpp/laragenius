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

    private static string $destination_path;

    private static bool $force_overwrite = false;

    public static function forceOverwrite(bool $force = true): void
    {
        self::$force_overwrite = $force;
    }

    public static function addGenerator(string $generator): void
    {
        self::$generators[] = $generator;
    }

    public static function setDestinationPath(string $path): void
    {
        self::$destination_path = $path;
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
    }

    public static function getDestinationPath(): string
    {
        if (isset(self::$destination_path)) {
            return self::$destination_path;
        }

        return base_path();
    }

    public static function isForcingOverwrite(): bool
    {
        return self::$force_overwrite;
    }
}
