<?php

namespace S4mpp\Laragenius\Contracts;

use S4mpp\Laragenius\Stub;

interface Generator
{
    public function getDestinationPath(): string;

    public function getFilename(): string;

    public function getStubFile(): string;

    public function mountFile(Stub $stub): void;
}
