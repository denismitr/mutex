<?php

namespace Denismitr\Mutex\Lock;


use Denismitr\Mutex\Contracts\LockInterface;
use Denismitr\Mutex\Errors\LockReleaseError;
use Predis;

class PredisLock extends LockAbstract implements LockInterface
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
     * @var int
     */
    private $timeout;

    /**
     * @var string
     */
    private $token;

    /**
     * PredisLock constructor.
     * @param Predis\Client $client
     * @param string $key
     * @param int $timeout
     */
    public function __construct(Predis\Client $client, string $key, int $timeout = 0)
    {
        $this->client = $client;
        $this->key = "LockAbstract:{$key}";
        $this->timeout = $timeout;
        $this->token = uniqid(true);
    }

    /**
     * Obtain the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockAcquireError
     * @return void
     */
    public function acquire()
    {
        if ( $this->client->getset("{$this->key}:start", 1) !== 1 ) {
            $this->client->lpush($this->key, [$this->token]);
        }

        $this->client->blpop([$this->key], $this->timeout);

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
        if ( ! $this->client->exists("{$this->key}:start") ) {
            throw new LockReleaseError(
                "Failed to release the lock. Check the validity of your file handle"
            );
        }

        $this->client->lpush($this->key, [$this->token]);

        $this->client->del(["{$this->key}:start", $this->key]);

        $this->acquired = false;
    }
}
