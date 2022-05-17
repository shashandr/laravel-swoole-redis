<?php
/**
 * 功能说明
 * @author: falco, antyblin
 * Date: 4/26/21
 * Time: 1:42 PM
 */

namespace Antyblin\SwooleRedis;

use Closure;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Redis\Events\CommandExecuted;
use Swoole\Database\RedisPool;

class SwooleRedisPoolConnection extends Connection
{

    /**
     * @var RedisPool
     */
    protected RedisPool $pool;


    public function __construct(RedisPool $pool)
    {
        $this->pool   = $pool;
        $this->client = null;
    }


    /**
     * Subscribe to a set of given channels for messages.
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $method
     *
     * @return void
     */
    public function createSubscription($channels, Closure $callback, $method = 'subscribe')
    {
        $loop = $this->pubSubLoop();

        call_user_func_array([$loop, $method], (array) $channels);

        foreach ($loop as $message) {
            if ($message->kind === 'message' || $message->kind === 'pmessage') {
                call_user_func($callback, $message->payload, $message->channel);
            }
        }

        unset($loop);
    }


    /**
     * Get the underlying Redis client.
     *
     * @return mixed
     */
    public function client()
    {
        return $this->pool->get();
    }


    /**
     * Run a command against the Redis database.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function command($method, array $parameters = [])
    {
        $start = microtime(true);

        $client = $this->pool->get();
        $result = $client->{$method}(...$parameters);
        $this->pool->put($client);

        $time = round((microtime(true) - $start) * 1000, 2);

        if (isset($this->events)) {
            $this->event(new CommandExecuted($method, $parameters, $time, $this));
        }

        return $result;
    }
}
