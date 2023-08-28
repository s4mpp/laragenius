<?php
namespace S4mpp\Laragenius;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

abstract class FileManipulation
{
	public static function putContentFile(string $stub_name, string $destination, $stub_variables = [])
    {
        $filesystem = new Filesystem;

        $content = self::getStubContents($stub_name, $stub_variables);

		$content = preg_replace("/\n    \n\n/", "\n", $content);
        $content = preg_replace("/\n\n\n/", "\n", $content);


        $filesystem->put($destination, $content);

    }

    public static function getStubContents(string $stub_name, array $stub_variables = [])
    {
		$origin_stub_content = file_get_contents(__DIR__.'/../stubs/'.$stub_name.'.stub', true);

        foreach($stub_variables as $search => $replace)
        {
            $origin_stub_content = str_replace('{{ '.$search.' }}', $replace, $origin_stub_content);
        }

        return $origin_stub_content;
    }

    public static function findResourceFile(string $resource_name): ?Collection
	{
		$file_config = 'laragenius'.DIRECTORY_SEPARATOR.strtolower($resource_name).'.json';

		if(!file_exists($file_config))
		{
			return null;
		}

		$config = json_decode(file_get_contents($file_config));

		return collect($config);
	}
}