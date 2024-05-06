<?php

namespace S4mpp\Laragenius;

final class Laragenius
{
	private static array $generators = [];

    public static function addGenerator(string $generator): void
	{
		self::$generators[] =  $generator;
	}

	public static function getGenerators(): array
	{
		return self::$generators;
	}
}
