<?php

namespace Tests;

use Denismitr\Mutex\Lock\LockAbstract;
use PHPUnit\Framework\TestCase;

class LockAbstractTest extends TestCase
{
    private $lock;

    public function setUp()
    {
        parent::setUp();

        $this->lock = $this->getMockForAbstractClass(LockAbstract::class);
    }

    /** @test */
    public function it_runs_acquire_before_running_the_callback()
    {
        $this->lock->expects($this->once())->method('acquire');

        $this->lock->safe(function() {});
    }

    /** @test */
    public function it_releases_lock_after_callback_has_been_executed()
    {
        $this->lock->expects($this->once())->method('release');
        $this->lock->safe(function() {});
    }

    /** @test */
    public function it_calls_release_if_an_exception_is_thrown_inside_callback()
    {
        $this->lock->expects($this->once())->method('release');

        try {
            $this->lock->safe(function() {
                throw new \Exception;
            });
        } catch (\Exception $e) {}
    }
}
