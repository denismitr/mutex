<?php

use Denismitr\Mutex\Errors\LockAcquireError;
use Denismitr\Mutex\Errors\LockReleaseError;

class SemaphoreLock extends Lock
{
    /**
     * @var resource
     */
    private $semaphoreId;

    /**
     * Instantiate with the semaphore id.
     *
     * Use sem_get() to create the semaphore id.
     *
     * Example:
     * <code>
     * $semaphoreId = sem_get(ftok(__FILE__, "a"));
     * $mutex = MutexFactory::makeSemaphore($semaphoreId);
     * </code>
     *
     * @param resource semaphore The semaphore id.
     * @throws \InvalidArgumentException The semaphore id is not a valid resource.
     */
    public function __construct(resource $semaphoreId)
    {
        if (!is_resource($semaphoreId)) {
            throw new \InvalidArgumentException(
                "The semaphore id is not a valid resource."
            );
        }

        $this->semaphoreId = $semaphoreId;
    }

    /**
     * Obtain the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockAcquireError
     * @return void
     */
    public function acquire()
    {
        if ( ! sem_acquire($this->semaphoreId) ) {
            throw new LockAcquireError(
                "Failed to acquire the lock. Check the validity of your semaphore id."
            );
        }
    }

    /**
     * Release the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockReleaseError
     * @return void
     */
    public function release()
    {
        if ( ! sem_release($this->semaphoreId) ) {
            throw new LockReleaseError(
                "Failed to release the lock. Check the validity of your semaphore id"
            );
        }
    }
}
