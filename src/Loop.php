<?php

namespace Denismitr\Mutex;

use Denismitr\Mutex\Exceptions\TimeoutError;

class Loop
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
     * @param callable $callback
     * @throws \Denismitr\Mutex\Exceptions\TimeoutError
     * @return mixed
     */
    public function run(callable $callback)
    {
        $this->loop = true;

        $minWait = 100;

        $timeout = microtime(true) + $this->timeout;

        for ($i = 0; $this->loop && microtime(true) < $timeout; $i++) {
            // Call the callback and pass this as only argument
            $result = $callback($this);

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

        return $result;
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
