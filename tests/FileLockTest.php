<?php

namespace Tests;

use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Mutex;
use PHPUnit\Framework\TestCase;

class FileLockTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject The Mutex mock.
     */
    private $lock;

    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_acquire_and_release_lock()
    {
        $fh = fopen("./tests/tmp/lock.lock", "r+");

        $lock = new FileLock($fh);

        $lock->acquire();

        $this->assertTrue($lock->isAcquired());

        $this->assertInternalType(resource, fopen("./tests/tmp/lock.lock", "r+"));

        $lock->release();

        $this->assertFalse($lock->isAcquired());
    }
}
