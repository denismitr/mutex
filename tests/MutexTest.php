<?php

namespace Tests;

use Denismitr\Mutex\Mutex;
use PHPUnit\Framework\TestCase;

class MutexTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $mutex = new Mutex();

        $this->assertInstanceOf(Mutex::class, $mutex);
    }
}
