<?php

namespace Tests;

use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Lock\Lock;
use Denismitr\Mutex\Utilities\Check;
use PHPUnit\Framework\TestCase;

class CheckTest extends TestCase
{
    /**
     * @var Lock
     */
    public $lock;

    protected function setUp()
    {
        parent::setUp();

        $this->lock = $this->createMock(FileLock::class);
        $this->check = new Check($this->lock);
    }

    /**
     * Tests that the lock will not be acquired when a test fails.
     *
     * @test
     */
    public function it_fails_to_acquire_lock_if_callable_returns_false()
    {
        $this->lock->expects($this->never())->method("safe");

        $this->check->try(function () {
            return false;
        });

        $this->check->then(function () {
            $this->fail();
        });
    }

    /**
     * Tests that the check and execution are in the same lock.
     *
     * @test
     */
    public function it_locks_both_the_target_and_the_given_callback()
    {
        $lock  = 0;
        $check = 0;

        $this->lock->expects($this->once())
                ->method("safe")
                ->willReturnCallback(function (callable $block) use (&$lock) {
                    $lock++;
                    $block();
                    $lock++;
                });

        $this->check->try(function () use (&$lock, &$check) {
            if ($check == 1) {
                $this->assertEquals(1, $lock);
            }

            $check++;

            return true;
        });

        $this->check->then(function () use (&$lock) {
            $this->assertEquals(1, $lock);
        });

        $this->assertEquals(2, $check);
    }

    /**
     * Returns failed checks.
     *
     * @return callable[][]
     */
    public function provideFailedChecks()
    {
        $checkCounter = 0;

        return [
            [function () {
                return false;
            }],

            [function () use (&$checkCounter) {
                $result = $checkCounter == 1;
                $checkCounter++;
                return $result;
            }],
        ];
    }

    /**
     * Tests that the code is not executed if the first or second check fails.
     *
     * @param callable $check The check.
     *
     * @dataProvider provideFailedChecks
     *
     * @test
     */
    public function it_does_not_execute_the_callable_if_one_of_the_checks_fails(callable $target)
    {
        $this->lock->expects($this->never())->method("safe");

        $this->check->try($target);

        $this->check->then(function () {
            $this->fail();
        });
    }

    /**
     * Tests that the callable gets executed if the checks are true.
     *
     * @test
     */
    public function it_executes_callable_on_success_checks()
    {
        $this->lock->expects($this->once())
                ->method("safe")
                ->willReturnCallback(function (callable $block) {
                    return call_user_func($block);
                });

        $this->check->try(function () {
            return true;
        });

        $executed = false;

        $this->check->then(function () use (&$executed) {
            $executed = true;
        });

        $this->assertTrue($executed);
    }
}
