<?php
/**
 * @author: falco, antyblin
 * Date: 5/17/22
 * Time: 10:53 AM
 */

namespace Shashandr\SwooleRedis;

use Illuminate\Contracts\Redis\Factory;
use InvalidArgumentException;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

class RedisPoolManager implements Factory
{
    
    /**
     * The Redis server configurations.
     *
     * @var array
     */
    protected $config;
    
    /**
     * The Redis connections.
     *
     * @var mixed
     */
    protected $connections;
    
    
    /**
     * Create a new Redis manager instance.
     *
     * @param string $driver
     * @param array  $config
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    
    /**
     * Get a Redis pool connection by name.
     *
     * @param string|null $name
     *
     * @return
     */
    public function connection($name = null)
    {
        $name = $name ?: 'default';
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }
        
        return $this->connections[$name] = $this->resolve($name);
    }
    
    
    /**
     * Resolve the given connection by name.
     *
     * @param string|null $name
     *
     * @return \Illuminate\Redis\Connections\Connection
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name = null)
    {
        $name = $name ?: 'default';
        
        $options = $this->config['options'] ?? [];
        
        if (isset($this->config[$name])) {
            return $this->connect($this->config[$name], $options);
        }
        
        throw new InvalidArgumentException(
            "Redis connection [{$name}] not configured.",
        );
    }
    
    
    /**
     * @param $config
     * @param $options
     *
     * @return SwooleRedisPoolConnection
     */
    public function connect(array $config, $options = [])
    {
        $redisConfig = (new RedisConfig())
            ->withHost($config['host'])
            ->withDbIndex($config['database'])
            ->withPort($config['port'])
            ->withAuth($config['password']);
        
        return new SwooleRedisPoolConnection(new RedisPool($redisConfig, $config['pool_size'] ?? 64));
    }
    
    
    /**
     * Return all created connections.
     *
     * @return array
     */
    public function connections()
    {
        return $this->connections;
    }
}
