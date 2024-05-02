<?php

namespace S4mpp\Laragenius\Contracts;

interface FakerInterface
{
    public function getFaker(string $field_name): string;
}
