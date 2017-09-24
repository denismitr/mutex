<?php

namespace Denismitr\Mutex\Utilities;

use Closure;
use Denismitr\Mutex\Mutex;

class DoubleCheck
{
    /**
     * @var Mutex The mutex.
     */
    private $mutex;

    /**
     * @var Closure The target to check.
     */
    private $target;

    /**
     * Sets the mutex.
     *
     * @param Mutex $mutex The mutex.
     */
    public function __construct(Mutex $mutex)
    {
        $this->mutex = $mutex;
    }

    /**
     * Sets the target callback.
     *
     * @param Closure $target The target callback to check.
     * @internal
     */
    public function try(Closure $target)
    {
        $this->target = $target;
    }

    /**
     * Executes a code only if the target callable returns true.
     *
     * Both the target and the passed callable executions are locked by a mutex.
     * Only if the target fails, the method returns before acquiring a lock.
     *
     * @param Closure $callable
     */
    public function then(Closure $callable)
    {
        if ( ! call_user_func($this->target) ) {
            return;
        }

        $this->mutex->safe(function () use ($callable) {
            if ( call_user_func($this->target) ) {
                call_user_func($callable);
            }
        });
    }
}
