<?php
namespace S4mpp\Laragenius;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;

abstract class FileManipulation
{
	public static function getStubContents(string $stub_name, array $stub_variables = [])
    {
		$origin_stub_content = file_get_contents('stubs/'.$stub_name.'.stub', true);

        foreach($stub_variables as $search => $replace)
        {
            $origin_stub_content = str_replace('{{ '.$search.' }}', $replace, $origin_stub_content);
        }

        return $origin_stub_content;
    }

	public static function putContentFile(string $filename, string $destination = null, string $content = null): string
    {
        $filesystem = new Filesystem;

        $path = join('/', array_filter([$destination, $filename.'.php']));
        
        $filesystem->put(base_path($path), $content);

        return $path;
    }
}