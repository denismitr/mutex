<?php

namespace Tests;

use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Mutex;
use PHPUnit\Framework\TestCase;

class MutexTest extends TestCase
{
    /** @test */
    public function it_can_create_file_lock()
    {
        $lock = Mutex::fileLock(__FILE__);

        $this->assertInstanceOf(FileLock::class, $lock);
    }
}
