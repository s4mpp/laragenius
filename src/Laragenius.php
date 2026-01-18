<?php

namespace S4mpp\Laragenius;

use Illuminate\Filesystem\Filesystem;
use S4mpp\Laragenius\Generators\ModelGenerator;
use S4mpp\Laragenius\Generators\SeederGenerator;
use S4mpp\Laragenius\Generators\FactoryGenerator;

final class Laragenius
{
    /**
     * @var array<string>
     */
    private static array $generators = [];

    private static string $base_path;

    // private static bool $force_overwrite = false;

    // public static function forceOverwrite(bool $force = true): void
    // {
    //     self::$force_overwrite = $force;
    // }

    public static function addGenerator(string $generator): void
    {
        self::$generators[] = $generator;
    }

    public static function setBasePath(string $path): void
    {
        self::$base_path = $path;
    }

    public static function getBasePath(): string
    {
        if (isset(self::$base_path)) {
            return self::$base_path;
        }

        return base_path();
    }

    /**
     * @return array<string>
     */
    public static function getGenerators(): array
    {
        $built_in_generators = [
            ModelGenerator::class,
            FactoryGenerator::class,
            SeederGenerator::class,
        ];

        return array_merge($built_in_generators, self::$generators);
    }

    // public static function createFile(string $filename, string $content): string
    // {
    //     $filesystem = new Filesystem;

    //     $path = implode('/', array_filter([self::getBasePath(), $filename.'.php']));

    //     // if (! Laragenius::isForcingOverwrite() && $filesystem->exists($full_path)) {
    //     //     throw new \Exception('File already exists');
    //     // }

    //     // TODO create folder if not exists

    //     $filesystem->put($path, $content);

    //     return $path;
    // }

    // public static function flushGenerators(): void
    // {
    //     self::$generators = [];
    // }

    // public static function isForcingOverwrite(): bool
    // {
    //     return self::$force_overwrite;
    // }
}
