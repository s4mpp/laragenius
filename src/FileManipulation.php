<?php
namespace S4mpp\Laragenius;

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
}