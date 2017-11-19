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

    private $redis;

    protected function setUp()
    {
        parent::setUp();

        $this->redis = new Client;

        $this->lock = Mutex::pRedisLock($this->redis, "my-key", 20);
    }

    /** @test */
    public function it_can_acquire_and_release_lock()
    {
        $this->lock->acquire();

        $this->assertEquals(1, $this->redis->get("Lock:my-key:start"));

        $this->assertTrue($this->lock->isAcquired());

        $this->lock->release();

        $this->assertFalse($this->lock->isAcquired());
        $this->assertEquals(0, $this->redis->exists("Lock:my-key:start"));
    }
}
