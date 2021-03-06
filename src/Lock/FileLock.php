<?php

namespace Denismitr\Mutex\Lock;


use Denismitr\Mutex\Contracts\LockInterface;

class FileLock extends LockAbstract implements LockInterface
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
    public function __construct($fh)
    {
        if ( ! is_resource($fh) ) {
            throw new \InvalidArgumentException(
                "The file handle is not a valid resource."
            );
        }

        $this->fh = $fh;
    }


    /**
     * Destructor
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->fh)) {
            fclose($this->fh);
        }
    }

    /**
     * Obtain the lock
     *
     * @throws \Denismitr\Mutex\Errors\LockAcquireError
     * @return void
     */
    public function acquire()
    {
        if ( ! flock($this->fh, LOCK_EX) ) {
            throw new LockAcquireError(
                "Failed to acquire the lock. Check the validity of your file handle"
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
        if ( ! flock($this->fh, LOCK_UN) ) {
            throw new LockReleaseError(
                "Failed to release the lock. Check the validity of your file handle"
            );
        }

        $this->acquired = false;
    }
}
