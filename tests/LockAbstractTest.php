<?php

namespace Tests;

use Denismitr\Mutex\Lock\LockAbstract;
use PHPUnit\Framework\TestCase;

class LockAbstractTest extends TestCase
{
    private $mutex;

    public function setUp()
    {
        parent::setUp();

        $this->mutex = $this->getMockForAbstractClass(LockAbstract::class);
    }

    /** @test */
    public function it_runs_acquire_before_running_the_callback()
    {
        $this->mutex->expects($this->once())->method('acquire');

        $this->mutex->safe(function() {});
    }

    /** @test */
    public function it_releases_lock_after_callback_has_been_executed()
    {
        $this->mutex->expects($this->once())->method('release');

        $this->mutex->safe(function() {});
    }

    /** @test */
    public function it_calls_release_if_an_exception_is_thrown_inside_callback()
    {
        $this->mutex->expects($this->once())->method('release');

        try {
            $this->mutex->safe(function() {
                throw new \Exception;
            });
        } catch (\Exception $e) {}
    }
}
