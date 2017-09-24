<?php

namespace Denismitr\Mutex\Lock;

use Closure;
use Predis;

class PredisLock extends Lock
{
    /**
     * @var resource
     */
    private $client;

    /**
     * @var string
     */
    private $key;

    /**
     * Instantiate from file handle.
     *
     * @param resource $fileHandle
     * @throws \InvalidArgumentException
     */
    public function __construct(Predis\Client $client, string $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    /**
     * Obtain the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockAcquireError
     * @return void
     */
    public function acquire()
    {
        if ( ! $this->client->setnx($key, true) ) {
            throw new LockAcquireError(
                "Failed to acquire the lock. Check the validity of your key"
            );
        }

        $this->acquired = true;
    }

    /**
     * Release the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockReleaseError
     * @return void
     */
    public function release()
    {
        if ( ! $this->client->exists($this->key) ) {
            throw new LockReleaseError(
                "Failed to release the lock. Check the validity of your file handle"
            );
        }

        $this->client->delete($this->key);

        $this->acquired = false;
    }
}
