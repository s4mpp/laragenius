<?php
namespace Samuelpacheco\Laragenius;

use Illuminate\Support\Str;

class Utils
{
	public static function nameModel(string $name_resource)
	{
		return Str::ucfirst(Str::camel(Str::lower($name_resource)));
	}
}