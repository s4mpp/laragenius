<?php
namespace S4mpp\Laragenius;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

abstract class FileManipulation
{
	public static function putContentFile(string $stub_name, string $destination, $stub_variables = [])
    {
        $filesystem = new Filesystem;

        $path = explode(DIRECTORY_SEPARATOR, $destination);

        array_pop($path);

        $folder = join(DIRECTORY_SEPARATOR, $path);
        
        if(!$filesystem->exists($folder))
        {
            $filesystem->makeDirectory($folder);
        }
        
        $content_file = self::getStubContents($stub_name, $stub_variables);

		$content_file = preg_replace("/\n    \n\n/", "\n", $content_file);
        $content_file = preg_replace("/\n\n\n/", "\n", $content_file);

        $filesystem->put($destination, $content_file);
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
		$file_config = 'laragenius'.DIRECTORY_SEPARATOR.strtolower($resource_name);

		if(!file_exists($file_config))
		{
			return null;
		}

		$config = json_decode(file_get_contents($file_config));

		return collect($config);
	}

    public static function getResourcesFiles(): ?array
	{
        $files = [];

		foreach(glob('laragenius'.DIRECTORY_SEPARATOR.'*.json') as $file)
		{
            $name_file = explode(DIRECTORY_SEPARATOR, $file);

            $files[end($name_file)] = json_decode(file_get_contents($file));
		}

        return $files;
	}
}