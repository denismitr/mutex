<?php

namespace Denismitr\Mutex;

use Closure;
use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Lock\Lock;
use Denismitr\Mutex\Lock\PredisLock;
use Predis\Client;

class Mutex
{
    public static function fileLock(string $filename) : FileLock
    {
        $fh = fopen($filename, "r+");

        return new FileLock($fh);
    }

    public static function pRedisLock(string $key) : PredisLock
    {
        $client = new Client();

        return new PredisLock($client, $key);
    }
}
