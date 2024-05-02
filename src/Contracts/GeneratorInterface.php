<?php

namespace S4mpp\Laragenius\Contracts;

use S4mpp\Laragenius\Stub;

interface GeneratorInterface
{
    public function getNamespace(): string;

    public function getFilename(): string;

    public function getContent(): Stub;
}
