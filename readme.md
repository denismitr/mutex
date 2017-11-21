# PHP Mutex Library

## Author
[Denis Mitrofanov](https://thecollection.ru)

### Installation

```bash
composer require denismitr/mutex
```

### Usage

Initialization with factory:
- File lock
```php
$lock = MutexFactory::fileLock(__FILE__); // or some other file name like /tmp/some-id
```
- Semaphore lock (linux only)
```php
$lock = MutexFactory::semaphoreLock(__FILE__); // or some other file name like /tmp/some-id
```
- PRedis lock
```php
$this->redis = new Client([
    'host' => 'localhost',
    'port' => 6379,
    'database' => 0,
]);

$this->lock = MutexFactory::pRedisLock($this->redis, "some-key", 20);
```
This far only these types of locks are supported

Using the lock instances

```php
$lock->acquire();

// Do some critical stuff here

$lock->release();
```

With closures
```php
$lock->safe(function() {
    // Lock will be acuqired and released automatically
    
    // Do some critical stuff safely
});
```

Performing a check first
```php
$lock->try(function() use ($room, $from, $to) {
    // e.g
    return $room->isFree($from, $to);
})->then(function() use ($room, $from, $to) {
    // e.g.
    // Lock is aquired automatically
    
    $room->book($from, $to);
})->fail(function() use ($user) {
    // this callback will fire if the condition in try closure fails
    // e.g.
    $user->notify("Room is not available for requested time period.");
});
```

Looping in the safe, locked mode
```php
$lock->loop($timeoutInSeconds, function($loop, $i) ($user, $ads) {
    // lock is acquired and released automatically when loop is done
    // e.g. send out only 10 ads to user friends
    
    // Laravel collections example
    $user->friends->each->notify($adds->random());
    
    if ($i >= 10) {
        $loop->stop();
    }
});
```


