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
            try {
                $this->redis = new RedisClient('tcp://127.0.0.1:6379');
                $this->redis->select(15);
                $this->redis->flushdb();
            } catch (\Exception $e) {
                $this->markTestIncomplete(
                    'Redis Server instance not found!'
                );
            }

        }
        return $this->redis;
    }

    public function tearDown()
    {
        unset($this->redis);
    }

    public function keyValuesProvider()
    {
        $cases = array();
        $total = 20;
        for ($i = 0; $i < $total; $i++) {
            $cases[] = array("field$i", md5(uniqid(rand(), true)));
        }

        return $cases;
    }
}
