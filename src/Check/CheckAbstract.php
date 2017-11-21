<?php

namespace Denismitr\Mutex\Check;


use Closure;
use Denismitr\Mutex\Contracts\LockInterface;

class CheckAbstract
{
    /**
     * @var LockInterface.
     */
    protected $lock;

    /**
     * @var Closure
     */
    protected $condition;

    /**
     * @var Closure
     */
    protected $failCallback;

    /**
     * Sets the lock.
     *
     * @param LockInterface $lock.
     */
    public function __construct(LockInterface $lock)
    {
        $this->lock = $lock;
    }

    /**
     * @param Closure $condition
     */
    public function try(Closure $condition)
    {
        $this->condition = $condition;
    }

    /**
     * @param Closure $callback
     */
    public function fail(Closure $callback)
    {
        $this->failCallback = $callback;
    }

    /**
     * Call the fail callback if set
     */
    protected function callFailCallback()
    {
        if ($this->failCallback) {
            call_user_func($this->failCallback);
        }
    }
}