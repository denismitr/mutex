<?php

namespace Denismitr\Mutex\Contracts;

use Closure;

interface LockInterface
{
    /**
     * Obtain the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockAcquireError
     * @return void
     */
    public function acquire();

    /**
     * Release the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockReleaseError
     * @return void
     */
    public function release();

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function safe(Closure $callback);

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function try(Closure $callback) : CheckInterface;

    /**
     * @param int $timeout
     * @param Closure $callback
     * @return mixed
     */
    public function loop(int $timeout, Closure $callback);
}
