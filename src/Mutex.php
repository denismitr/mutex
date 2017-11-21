<?php

namespace Denismitr\Mutex;

use Closure;
use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Lock\LockAbstract;
use Denismitr\Mutex\Lock\PredisLock;
use Denismitr\Mutex\Lock\SemaphoreLock;
use Predis\Client;

class Mutex
{
    /**
     * @param string $filename
     * @return FileLock
     */
    public static function fileLock(string $filename) : FileLock
    {
        $fh = fopen($filename, "r+");

        return new FileLock($fh);
    }

    /**
     * @param Client $client
     * @param string $key
     * @param int $timeout
     * @return PredisLock
     */
    public static function pRedisLock(Client $client, string $key, int $timeout = 0) : PredisLock
    {
        return new PredisLock($client, $key, $timeout);
    }

    /**
     * @param string $filename
     * @return SemaphoreLock
     */
    public static function semaphoreLock(string $filename) : SemaphoreLock
    {
        $semaphoreId = sem_get(ftok($filename, "a"));

        return new SemaphoreLock($semaphoreId);
    }
}
