<?php

namespace Denismitr\Mutex\Lock;


use Closure;
use Denismitr\Mutex\Check\DoubleCheck;
use Denismitr\Mutex\Contracts\CheckInterface;
use Denismitr\Mutex\Loop\Loop;

abstract class LockAbstract
{
    protected $acquired = false;

    /**
     * @return bool
     */
    public function isAcquired() : bool
    {
        return $this->acquired;
    }

    /**
     * @param Closure $target
     * @return CheckInterface
     */
    public function try(Closure $target) : CheckInterface
    {
        $check = new DoubleCheck($this);

        $check->try($target);

        return $check;
    }

    /**
     * Execute the callback in the exclusively locked mode
     *
     * @param Closure $callback
     * @return mixed
     */
    public function safe(Closure $callback)
    {
        $this->acquire();

        try {
            return $callback();
        } finally {
            $this->release();
        }
    }

    /**
     * @param int $timeout
     * @param Closure $callback
     * @return mixed|null
     */
    public function loop(int $timeout, Closure $callback)
    {
        $loop = new Loop($timeout);

        $this->acquire();

        try {
            return $loop->run($callback);
        } finally {
            $this->release();
        }
    }

    /**
     * Obtain the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockAcquireError
     * @return void
     */
    public abstract function acquire();

    /**
     * Release the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockReleaseError
     * @return void
     */
    public abstract function release();
}
