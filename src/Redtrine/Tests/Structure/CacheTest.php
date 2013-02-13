<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Tests\RedtrineTestCase;
use Redtrine\Structure\Cache;

class CacheTest extends RedtrineTestCase
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var string $testValue
     */
    protected $testValue;

    public function setUp()
    {
        parent::setUp();

        $this->cache = new Cache('TestBitField');
        $this->cache->setClient($this->getRedisClient());

        $this->testValue = serialize(array(1,2,3));
        $this->cache->set($this->testValue);
    }

    public function tearDown()
    {
        $this->cache->destroy();

        parent::tearDown();
    }

    public function testCacheWorks()
    {
        $this->assertEquals($this->testValue, $this->cache->get());
    }

    public function testCacheCanBeRemoved()
    {
        $this->cache->destroy();
        $this->assertEmpty($this->cache->get());
    }

    public function testCacheGetsExpired()
    {
        $this->cache->set($this->testValue, 1);
        $this->assertEquals($this->testValue, $this->cache->get());
        sleep(2);
        $this->assertEmpty($this->cache->get());
    }
}