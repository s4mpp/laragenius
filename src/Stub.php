<?php

namespace S4mpp\Laragenius;

use Illuminate\Filesystem\Filesystem;

final class Stub
{
    private string $content;

    /**
     * @var array<string,string|int|null>
     */
    private array $variables = [];

    // private string $original_content;

    public function __construct(string $file/*, bool $use_local_path = true*/)
    {
        // if ($use_local_path) {
        //     $file = __DIR__.'/../stubs/'.$file;
        // }

        $file = file_get_contents($file, true);

        // $this->original_content =
        $this->content = (string) $file;
    }

    public function setVariable(string $key, string|int|null $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    // public function __toString()
    // {
    //     return $this->content;
    // }

    // public function reset(): self
    // {
    //     $this->content = $this->original_content;

    //     return $this;
    // }

    public function fill(): self
    {
        foreach ($this->variables as $key => $value) {
            $this->content = str_replace('{{ '.$key.' }}', (string) $value, $this->content);
        }

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    // public function put(string $filename, ?string $destination = null): string
    // {
    //     $filesystem = new Filesystem;

    //     $path = implode('/', array_filter([$destination, $filename.'.php']));

    //     $destination_path = Laragenius::getDestinationPath();

    //     $full_path = $destination_path.'/'.$path;

    //     if (! Laragenius::isForcingOverwrite() && $filesystem->exists($full_path)) {
    //         throw new \Exception('File already exists');
    //     }

    //     // TODO create folder if not exists

    //     $filesystem->put($full_path, $this->content);

    //     return $path;
    // }
}
