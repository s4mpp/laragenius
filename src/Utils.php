<?php
namespace S4mpp\Laragenius;

use Illuminate\Support\Str;

class Utils
{
	public static function nameTable(string $resource_name)
	{
		return Str::snake(Str::plural($resource_name));
	}

	public static function translate(string $string_to_translate, $translator): string
	{
		$str_no_underline = Str::replace('_', ' ', $string_to_translate);

		$str_translated = $translator->translate($str_no_underline);

		return  Str::ucfirst(Str::lower($str_translated));
	}
}