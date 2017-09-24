<?php

namespace Denismitr\Mutex;

use Closure;
use Denismitr\Mutex\Lock\FileLock;
use Denismitr\Mutex\Lock\Lock;

class Mutex
{
    public static function fileLock(string $filename) : FileLock
    {
        $fh = fopen($filename, "r+");

        return new FileLock($fh);
    }
}
