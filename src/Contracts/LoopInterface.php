<?php

namespace Denismitr\Mutex\Contracts;


use Closure;

interface LoopInterface
{
    /**
     * @param Closure $callback
     * @return mixed|null
     */
    public function run(Closure $callback);

    /**
     * @return void
     */
    public function stop();
}