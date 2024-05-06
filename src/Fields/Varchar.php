<?php

namespace S4mpp\Laragenius\Fields;

use S4mpp\Laragenius\Stub;
use Illuminate\Support\Str;
use S4mpp\Laragenius\Contracts\FakerInterface;

class Varchar implements FakerInterface
{
    public function getFaker(string $field_name): Stub
    {
        if (Str::contains($field_name, 'email')) {
            return new Stub('stubs/factory/fakers/email');
        }

        if (Str::contains($field_name, 'name')) {
            return new Stub('stubs/factory/fakers/name');
        }

        if (Str::contains($field_name, 'password')) {
            return new Stub('stubs/factory/fakers/password');
        }

        if (Str::contains($field_name, 'token')) {
            return new Stub('stubs/factory/fakers/token');
        }

        if (Str::contains($field_name, 'phone')) {
            return new Stub('stubs/factory/fakers/phone');
        }

        return new Stub('stubs/factory/fakers/word');
    }
}
