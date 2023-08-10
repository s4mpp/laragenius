<?php
namespace S4mpp\Laragenius;

use Illuminate\Support\Str;

class Utils
{
	public static function nameTable(string $resource_name)
	{
		return Str::snake(Str::plural($resource_name));
	}
}