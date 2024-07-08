<?php

namespace S4mpp\Laragenius;

use Illuminate\Filesystem\Filesystem;

final class Stub
{
    private string $content;

    public function __construct(string $file, bool $use_local_path = true)
    {
        if ($use_local_path) {
            $file = __DIR__.'/../stubs/'.$file;
        }

        $file = file_get_contents($file.'.stub', true);

        $this->content = (string) $file;
    }

    public function __toString()
    {
        return $this->content;
    }

    /**
     * @param  array<string|Stub|null>  $stub_variables
     */
    public function fill(array $stub_variables = []): self
    {
        foreach ($stub_variables as $search => $replace) {
            $this->content = str_replace('{{ '.$search.' }}', (string) $replace, $this->content);
        }

        return $this;
    }

    public function put(string $filename, ?string $destination = null): string
    {
        $filesystem = new Filesystem;

        $path = implode('/', array_filter([$destination, $filename.'.php']));

        $destination_path = Laragenius::getDestinationPath();

        $full_path = $destination_path.'/'.$path;

        if (! Laragenius::isForcingOverwrite() && $filesystem->exists($full_path)) {
            throw new \Exception('File already exists');
        }

        // TODO create folder if not exists

        $filesystem->put($full_path, $this->content);

        return $path;
    }
}
