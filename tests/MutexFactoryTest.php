<?php

namespace Tests;

use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Lock\PredisLock;
use Denismitr\Mutex\Lock\SemaphoreLock;
use Denismitr\Mutex\MutexFactory;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class MutexFactoryTest extends TestCase
{
    /** @test */
    public function it_can_create_file_lock()
    {
        $lock = MutexFactory::fileLock(__FILE__);

        $this->assertInstanceOf(FileLock::class, $lock);
    }

    /** @test */
    public function it_can_create_a_semaphore_lock()
    {
        $lock = MutexFactory::semaphoreLock(__FILE__);

        $this->assertInstanceOf(SemaphoreLock::class, $lock);
    }

    /** @test */
    public function it_can_create_predis_lock()
    {
        $lock = MutexFactory::pRedisLock(new Client,'some-key');

        $this->assertInstanceOf(PredisLock::class, $lock);
    }
}
