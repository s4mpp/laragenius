<?php

namespace S4mpp\Laragenius\Fields;

use S4mpp\Laragenius\Stub;
use S4mpp\Laragenius\Contracts\FakerInterface;

class TinyInt implements FakerInterface
{
    public function getFaker(string $field_name): string
    {
        return new Stub('factory/fakers/random-digit');
    }
}
