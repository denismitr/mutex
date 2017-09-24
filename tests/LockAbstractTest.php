<?php

namespace Tests;

use Denismitr\Mutex\Utilities\DoubleCheck;
use Denismitr\Mutex\Lock\Lock;
use PHPUnit\Framework\TestCase;

class LockAbstractTest extends TestCase
{
    private $lock;

    public function setUp()
    {
        parent::setUp();

        $this->lock = $this->getMockForAbstractClass(Lock::class);

        // new DoubleCheck($this->lock);
    }

    /** @test */
    public function it_runs_acquire_before_running_the_callback()
    {
        $this->lock->expects($this->once())->method('acquire');

        $this->lock->ex(function() {});
    }

    // /** @test */
    // public function it_reflects_the_state_of_the_lock()
    // {
    //     $this->assertFalse($this->lock->isAcquired());

    //     $this->lock->ex(function() {
    //         $this->assertTrue($this->lock->isAcquired());
    //     });

    //     $this->assertFalse($this->lock->isAcquired());
    // }

    /** @test */
    public function it_releases_lock_after_callback_has_been_executed()
    {
        $this->lock->expects($this->once())->method('release');
        $this->lock->ex(function() {});
    }

    /** @test */
    public function it_calls_release_if_an_exception_is_thrown_inside_callback()
    {
        $this->lock->expects($this->once())->method('release');

        try {
            $this->lock->ex(function() {
                throw new \Exception;
            });
        } catch (\Exception $e) {}
    }
}
