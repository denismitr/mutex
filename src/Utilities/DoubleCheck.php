<?php

namespace Denismitr\Mutex\Utilities;

use Closure;
use Denismitr\Mutex\Lock\Lock;

class DoubleCheck
{
    /**
     * @var Lock.
     */
    private $lock;

    /**
     * @var Closure The target to check.
     */
    private $target;

    /**
     * Sets the lock.
     *
     * @param Lock $lock.
     */
    public function __construct(Lock $lock)
    {
        $this->lock = $lock;
    }

    /**
     * Sets the target callback.
     *
     * @param Closure $target The target callback to check.ex*/
    public function try(Closure $target)
    {
        $this->target = $target;
    }

    /**
     * Executes a code only if the target callable returns true.
     *
     * Both the target and the passed callable executions are locked by a lock.
     * Only if the target fails, the method returns before acquiring a lock.
     *
     * @param Closure $callable
     */
    public function then(Closure $callable)
    {
        if ( ! call_user_func($this->target) ) {
            return;
        }

        $this->lock->safe(function () use ($callable) {
            if ( call_user_func($this->target) ) {
                call_user_func($callable);
            }
        });
    }
}
