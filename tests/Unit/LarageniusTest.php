<?php

namespace S4mpp\Laragenius\Tests\Unit;

use S4mpp\Laragenius\Laragenius;
use S4mpp\Laragenius\Tests\TestCase;

class LarageniusTest extends TestCase
{
    public function test_add_and_get_generator(): void
    {
        Laragenius::addGenerator('TestGenerator');

        $generators = Laragenius::getGenerators();

        $this->assertIsArray($generators);
        $this->assertCount(1, $generators);
        $this->assertContains('TestGenerator', $generators);
    }
}
