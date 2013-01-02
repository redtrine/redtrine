<?php

namespace Redtrine\Tests;

use Predis\Client as RedisClient;

class RedtrineTestCase extends \PHPUnit_Framework_TestCase
{
    protected $redis;

    public function setUp()
    {
    }

    protected function getRedisClient()
    {
        if (null === $this->redis) {
            $this->redis = new RedisClient('tcp://127.0.0.1:6379');
            $this->redis->select(15);
            $this->redis->flushdb();
        }

        return $this->redis;
    }

    public function tearDown()
    {
        unset($this->redis);
    }
}
