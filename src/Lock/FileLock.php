<?php

namespace Denismitr\Mutex\Lock;

use Closure;

class FileLock extends LockAbstract
{
    /**
     * @var resource
     */
    private $fh;

    /**
     * Instantiate from file handle.
     *
     * @param resource $fileHandle
     * @throws \InvalidArgumentException
     */
    public function __construct(resource $fh)
    {
        if ( ! is_resource($fh) ) {
            throw new \InvalidArgumentException(
                "The file handle is not a valid resource."
            );
        }

        $this->fh = $fh;
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
                "Failed to acquire the lock. Check the validity of your semaphore id id"
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
