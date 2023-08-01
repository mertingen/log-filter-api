<?php

namespace App\Service;

use Redis;

/**
 * @method string|mixed|false get($key)
 * @method bool set($key, $value, $timeout = null)
 * @method bool setEx($key, $ttl, $value)
 * @method int del($key1, ...$otherKeys)
 * @method string[] keys(string $pattern)
 * @method int|bool|Redis exists($key)
 * @method bool sAdd($key, $member)
 * @method bool sRem($key, $member)
 * @method array sMembers($key)
 * @method bool sIsMember($key, $member)
 * @method int sCard($key)
 */
class CacheService
{
    public function __construct(private readonly Redis $redis)
    {

    }

    public function __call(string $name, array $arguments)
    {
        return $this->redis->$name(...$arguments);
    }

}