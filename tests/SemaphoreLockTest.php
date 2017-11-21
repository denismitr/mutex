<?php

namespace Tests;

use Denismitr\Mutex\Lock\SemaphoreLock;
use PHPUnit\Framework\TestCase;
use Tests\Traits\LockState;

class SemaphoreLockTest extends TestCase
{
    use LockState;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject The Mutex mock.
     */
    private $lock;

    protected function setUp()
    {
        parent::setUp();

        if (function_exists('sem_get')) {
            $this->semaphoreId = sem_get(
                ftok(__FILE__, "r")
            );

            $this->lock = new SemaphoreLock($this->semaphoreId);
        }

    }

    /** @test */
    public function it_can_acquire_and_release_lock()
    {
        if (function_exists('sem_get')) {
            $this->assertFalse($this->lock->isAcquired());

            $this->lock->acquire();

            $this->assertTrue($this->lock->isAcquired());

            $this->lock->release();

            $this->assertFalse($this->lock->isAcquired());
        } else {
            echo "Could not run the semaphore tests!";
        }
    }
}
