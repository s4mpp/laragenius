<?php

namespace S4mpp\Laragenius\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithWorkbench;
}
