<?php

namespace S4mpp\Laragenius;

final class Stub
{
    private string $content;

    /**
     * @var array<string,string|int|null>
     */
    private array $variables = [];

    public function __construct(string $file)
    {
        $file = file_get_contents($file, true);

        $this->content = (string) $file;
    }

    public function setVariable(string $key, string|int|null $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * @return array<string,string|int|null>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

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
}
