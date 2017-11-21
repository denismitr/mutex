<?php

namespace Tests;

use Denismitr\Mutex\Lock\FileLock;
use PHPUnit\Framework\TestCase;
use Tests\Traits\LockState;

class FileLockTest extends TestCase
{
    use LockState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject The Mutex mock.
     */
    private $lock;

    protected function setUp()
    {
        parent::setUp();

        $this->fh = fopen("./tests/tmp/lock.lock", "r+");

        $this->lock = new FileLock($this->fh);
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
