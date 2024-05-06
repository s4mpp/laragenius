<?php

namespace S4mpp\Laragenius;

use Illuminate\Filesystem\Filesystem;

final class Stub
{
    private string $content;

    public function __construct(private string $file)
    {
        $file = file_get_contents($file.'.stub', true);

        if (! $file) {
            return;
        }

        $this->content = $file;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function getNameFile(): string
    {
        return $this->file;
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

        $filesystem->put(base_path($path), $this->content);

        return $path;
    }
}
