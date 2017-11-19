<?php

namespace Denismitr\Mutex\Lock;

use Closure;
use Denismitr\Mutex\Utilities\Check;

abstract class Lock
{
    protected $acquired = false;

    protected $check;

    /**
     * @return bool
     */
    public function isAcquired() : bool
    {
        return $this->acquired;
    }

    /**
     * @param Closure $target
     * @return Check
     */
    public function try(Closure $target)
    {
        $this->check = $this->check ?: new Check($this);

        $this->check->try($target);

        return $this->check;
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
