<?php

namespace Denismitr\Mutex\Lock;

use Closure;

abstract class Lock
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
     * Execute the callback in the exclusively locked mode
     *
     * @param Closure $callback
     * @return mixed
     */
    public function ex(Closure $callback)
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
