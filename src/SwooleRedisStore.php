<?php
/**
 * User: falco, antyblin
 * Date: 5/17/22
 * Time: 5:47 PM
 */

namespace Antyblin\SwooleRedis;

use Illuminate\Cache\RedisStore;

class SwooleRedisStore extends RedisStore
{

    /**
     * @var RedisPoolManager
     */
    protected $redis;

    public $config = [];


    /**
     * SwooleRedisStore constructor.
     *
     * @param  RedisPoolManager  $redis
     * @param  string  $prefix
     * @param  string  $connection
     */
    public function __construct(RedisPoolManager $redis, $prefix = '', $connection = 'default')
    {
        parent::__construct($redis, $prefix, $connection);
    }


    /**
     * @param $key
     *
     * @return float|int|mixed|string|null
     */
    public function get($key)
    {
        $value = parent::get($key);

        //This is a little fix for laravel
        //since Swoole Redis returns false instead of null
        //when value doesn't exist
        if ($value === false) {
            $value = null;
        }

        return $value;
    }
}
