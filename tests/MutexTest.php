<?php

namespace Tests;

use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Lock\PredisLock;
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

    /** @test */
    public function it_can_create_predis_lock()
    {
        $lock = Mutex::pRedisLock('some-key');

        $this->assertInstanceOf(PredisLock::class, $lock);
    }
}
