<?php

namespace Denismitr\Mutex\Check;


use Closure;
use Denismitr\Mutex\Contracts\CheckInterface;

class DoubleCheck extends CheckAbstract implements CheckInterface
{
    /**
     * @param Closure $callable
     */
    public function then(Closure $callable)
    {
        if ( ! call_user_func($this->condition) ) {
            $this->callFailCallback();

            return;
        }

        $this->lock->safe(function () use ($callable) {
            if ( call_user_func($this->condition) ) {
                call_user_func($callable);
            } else {
                $this->callFailCallback();
            }
        });
    }
}
