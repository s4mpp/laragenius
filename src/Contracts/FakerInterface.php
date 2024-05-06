<?php

namespace S4mpp\Laragenius\Contracts;

use S4mpp\Laragenius\Stub;

interface FakerInterface
{
    public function getFaker(string $field_name): Stub;
}
