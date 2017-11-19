<?php

namespace Tests;

use Denismitr\Mutex\Mutex;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Tests\Traits\LockState;

class PRedisLockTest extends TestCase
{
    use LockState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject The Mutex mock.
     */
    private $lock;

    protected function setUp()
    {
        parent::setUp();

        $this->lock = Mutex::pRedisLock(new Client, "my-key", 20);
    }

    /** @test */
    public function it_can_acquire_and_release_lock()
    {
        $this->lock->acquire();

        $this->assertTrue($this->lock->isAcquired());

        $this->lock->release();

        $this->assertFalse($this->lock->isAcquired());
    }
}
