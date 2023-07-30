<?php
namespace Samuelpacheco\Laragenius;

use Illuminate\Support\Str;

class Utils
{
	public static function nameModel(string $resource_name)
	{
		return Str::ucfirst(Str::camel(Str::lower($resource_name)));
	}

	public static function nameTable(string $resource_name)
	{
		return Str::snake(Str::plural(Str::lower($resource_name)));
	}
}