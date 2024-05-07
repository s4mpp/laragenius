<?php

namespace S4mpp\Laragenius;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

final class Stub
{
    private string $content;

    public function __construct(private string $file)
    {
        $path = __DIR__.'/../stubs/';

        $file = file_get_contents($path.$file.'.stub', true);

        $this->content = $file;
    }

    public function __toString()
    {
        return $this->content;
    }

    /**
     * @param  array<string>  $stub_variables
     */
    public function fill(array $stub_variables = []): self
    {
        foreach ($stub_variables as $search => $replace) {
            $this->content = str_replace('{{ '.$search.' }}', $replace, $this->content);
        }

        return $this;
    }

    public function put(string $filename, ?string $destination = null): string
    {
        $filesystem = new Filesystem;

        $path = implode('/', array_filter([$destination, $filename.'.php']));

        $destination_path = Laragenius::getDestinationPath();

        dump($destination_path);

        $filesystem->put($destination_path.'/'.$path, $this->content);

        return $path;
    }
}
