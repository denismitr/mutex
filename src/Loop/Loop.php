<?php

namespace Denismitr\Mutex\Loop;


use Denismitr\Mutex\Contracts\LoopInterface;
use Denismitr\Mutex\Errors\TimeoutError;
use Closure;

class Loop implements LoopInterface
{
    /**
     * Timeout in seconds
     *
     * @var int
     */
    private $timeout;

    /**
     * Calling of the callback must be repeated
     *
     * @var bool
     */
    private $loop;

    /**
     * @param int $timeout
     */
    public function __construct(int $timeout = 3)
    {
        if ($timeout < 1) {
            throw new \InvalidArgumentException("Timeout cannot be less than 1 second");
        }

        $this->timeout = $timeout;
    }

    /**
     * Call the callback function until the time runs out.
     *
     * @param Closure $callback
     * @return mixed|null
     * @throws TimeoutError
     */
    public function run(Closure $callback)
    {
        $this->loop = true;

        $minWait = 100;

        $timeout = microtime(true) + $this->timeout;

        for ($i = 0; $this->loop && microtime(true) < $timeout; $i++) {
            // Call the callback and pass this as only argument
            $result = $callback($this, $i);

            if ( ! $this->loop ) {
                break;
            }

            $min = $minWait * pow(2, $i);
            $max = $min * 2;
            $timeToSleep = rand($max, $min);

            usleep($timeToSleep);
        }

        if (microtime(true) >= $timeout) {
            throw new TimeoutError("Timeout exceeded.");
        }

        return $result ?? null;
    }

    /**
     * Stop the looping
     *
     * @return void
     */
    public function stop()
    {
        $this->loop = false;
    }
}
