<?php

namespace Tests;

use Denismitr\Mutex\Lock\PredisLock;
use Denismitr\Mutex\MutexFactory;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Tests\Traits\LockState;

class PredisLockTest extends TestCase
{
    use LockState;

    /**
     * @var PredisLock
     */
    private $lock;

    private $redis;

    protected function setUp()
    {
        parent::setUp();

        $this->redis = new Client([
            'host' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ]);

        $this->lock = MutexFactory::pRedisLock($this->redis, "my-key", 20);
    }

    /** @test */
    public function it_can_acquire_and_release_lock()
    {
        $this->lock->acquire();

        $this->assertEquals(1, $this->redis->get("LockAbstract:my-key:start"));

        $this->assertTrue($this->lock->isAcquired());

        $this->lock->release();

        $this->assertFalse($this->lock->isAcquired());
        $this->assertEquals(0, $this->redis->exists("LockAbstract:my-key:start"));
    }

    /** @test */
    public function it_can_execute_a_closure()
    {
        $this->lock->safe(function () {
            $this->assertTrue($this->lock->isAcquired());
        });

        $this->assertFalse($this->lock->isAcquired());
    }

    /** @test */
    public function it_executes_code_if_condition_returns_true()
    {
        $this->lock->try(function () {
            static $execution;

            if (is_null($execution)) {
                $this->assertFalse($this->lock->isAcquired(), "Before acquire.");

                $execution = 1;
            } else {
                $this->assertTrue($this->lock->isAcquired(), "After acquire");
            }

            return true;
        })->then(function() {
            $this->assertTrue($this->lock->isAcquired());
        });

        $this->assertFalse($this->lock->isAcquired());
    }

    /** @test */
    public function it_does_not_execute_code_if_condition_returns_true()
    {
        $this->lock->try(function () {
            static $execution;

            if (is_null($execution)) {
                $this->assertFalse($this->lock->isAcquired(), "Before acquire.");

                $execution = 1;
            } else {
                $this->assertTrue($this->lock->isAcquired(), "After acquire");
            }

            return false;
        })->then(function() {
            $this->fail("Should not have been executed!");
        });

        $this->assertFalse($this->lock->isAcquired());
    }
}
