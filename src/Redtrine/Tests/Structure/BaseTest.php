<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\Base;
use Redtrine\Tests\RedtrineTestCase;

class BaseTest extends RedtrineTestCase
{
    /**
     * @var TestStructure
     */
    protected $structure;

    public function setUp()
    {
        parent::setUp();

        $this->structure = new TestStructure(md5(rand()));
        $this->structure->setClient($this->getRedisClient());
    }

    public function testRemove()
    {
        $this->assertNull($this->structure->getSet($value = rand()));
        $this->assertEquals($value, $this->structure->getSet(rand()));

        $this->assertEquals(1, $this->structure->destroy());
        $this->assertNull($this->structure->getSet(rand()));
    }
}

class TestStructure extends Base
{
    /**
     * Set the string value of a key and return its old value.
     */
    public function getSet($value)
    {
        return $this->getClient()->getset($this->key, $value);
    }
}
