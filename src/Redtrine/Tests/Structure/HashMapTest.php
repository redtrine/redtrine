<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Tests\RedtrineTestCase;
use Redtrine\Structure\HashMap;

class HashMapTest extends RedtrineTestCase
{
    /**
     * @var HashMap
     */
    protected $map;

    public function setUp()
    {
        parent::setUp();
        $this->map = new HashMap(rand());
        $this->map->setClient($this->getRedisClient());
        $this->map->removeAll();
    }

    public function testArrayAccessOffsetExists()
    {
        $this->assertFalse(isset($this->map[rand()]));
    }

    /**
     * @dataProvider arrayAccessOffsetGetSetProvider
     */
    public function testArrayAccessOffsetGetSet($offset, $value)
    {
        $this->map[$offset] = $value;
        $this->assertEquals($value, $this->map[$offset]);
    }

    function arrayAccessOffsetGetSetProvider()
    {
        return array(
            array(rand(), rand()),
            array(null, rand()),
            array(rand(), null),
        );
    }

    public function testArrayAccessOffsetUnset()
    {
        $this->map[$offset = rand()] = ($value = rand());
        $this->assertEquals($value, $this->map[$offset]);

        unset($this->map[$offset]);
        $this->assertFalse(isset($this->map[$offset]));
    }


    public function testCount()
    {
        $this->assertEquals(0, count($this->map));

        $map = $this->getRandomElements();
        $this->map->set($map);

        $this->assertEquals(count($map), count($this->map));

        $this->map->removeAll();
        $this->assertEquals(0, count($this->map));
    }

    public function testIterator()
    {
        $elements = $this->getRandomElements();
        $this->map->set($elements);

        foreach ($this->map as $field) {
            $this->assertContains($field, $elements);
        }
    }

    public function getElements()
    {
        $result = array();
        foreach ($this->getRandomElements() as $field => $value) {
            $result[] = array($field, $value);
        }

        return $result;
    }

    public function getRandomElements()
    {
        $result = array();
        $total = 20;
        for ($i = 0; $i < $total; $i++) {
            $result[$i+1 . "field"] = md5(uniqid(rand(), true));
        }

        return $result;
    }
}
