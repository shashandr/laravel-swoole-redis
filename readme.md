# Swoole Redis Pool cache and session driver for Laravel

Default Laravel redis connection may cause errors when running in Swoole coroutines.   
This package adds support of Swoole RedisPool as a cache and session driver for Laravel.   
This is a fork of `falcolee/laravel-swoole-redis` package, original idea belongs to https://github.com/falcolee.

## Installation
### Step 1: 
Install package
```shell
composer require antyblin/laravel-swoole-redis
```

### Step 2:   
Add `redis_pool` store to the `stores` section in `config/cache.php`:

```php
    'redis_pool' => [
        'driver' => 'redis',
        'connection' => 'default',
    ],
```

### Step 3:   
Change your redis driver or session driver to `redis_pool` in your `.env` file and that is it.

## Config
You may add additional parameter `'pool_size'` to the redis section in `config/database.php`.
This parameter sets maximum quantity of connections in RedisPool. 

```php
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT'),
        'database' => env('REDIS_CACHE_DB'),
        'pool_size' => env('REDIS_POOL_SIZE', 64)
    ],
```
