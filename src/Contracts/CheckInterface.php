<?php

namespace Denismitr\Mutex\Contracts;


use Closure;

interface CheckInterface
{
    /**
     * @param Closure $callback
     * @return mixed
     */
    public function try(Closure $callback);

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function then(Closure $callback);

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function fail(Closure $callback);
}