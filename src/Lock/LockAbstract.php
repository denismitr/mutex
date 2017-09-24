<?php

namespace Denismitr\Mutex\Lock;

use Closure;

abstract class LockAbstract
{
    /**
     * Execute the callback in the safe locked mode
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
