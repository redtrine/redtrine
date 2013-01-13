<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Tests\RedtrineTestCase;
use Redtrine\Structure\Hash;

class HashTest extends RedtrineTestCase
{
    /**
     * @var Redtrine\Structure\Hash
     */
    protected $hash;

    public function setUp()
    {
        parent::setUp();
        $this->hash = new Hash('hashName');
        $this->hash->setClient($this->getRedisClient());
        $this->hash->removeAll();
    }

    public function testKeysReturnEmptyArrayWhenKeyDoesNotExist()
    {
        $this->assertTrue(is_array($this->hash->keys()));
    }

    public function testKeysReturnArrayOfHashKeys()
    {
        $keys = array();
        foreach ($this->keyValuesProvider() as $item) {
            list($key, $value) = $item;
            $keys[] = $key;

            $this->hash->set($key, $value);
        }

        $this->assertEquals($keys, $this->hash->keys());
    }

    /**
     * @dataProvider keyValuesProvider
     */
    public function testSetWithSingleKeyValue($key, $value)
    {
        $this->hash->set($key, $value);

        $this->assertTrue($this->hash->contains($key));
        $this->assertEquals($value, $this->hash->get($key));
    }

    public function testSetWithMultipleKeyValuesPair()
    {
        foreach ($this->keyValuesProvider() as $item) {
            list($key, $value) = $item;
            $values[$key] = $value;
        }

        $this->hash->set($values);

        $this->assertEquals(array_keys($values), $this->hash->keys());
        $this->assertEquals(array_values($values), $this->hash->values());
    }

    /**
     * @dataProvider getElements
     */
    public function testGet($field, $value)
    {
        $this->hash->set($field, $value);
        $this->assertTrue($this->hash->contains($field));
        $this->assertEquals($this->hash->get($field), $value);
    }

    /**
     * @dataProvider getElements
     */
    public function testDelete($field, $value)
    {
        $this->hash->set($field, $value);
        $this->assertTrue($this->hash->contains($field));
        $this->assertEquals($this->hash->get($field), $value);

        $this->assertEquals(1, $this->hash->delete($field));
        $this->assertFalse($this->hash->contains($field));
        $this->assertNull($this->hash->get($field));
    }

    public function testExists()
    {
        $elements = $this->getRandomElements();
        foreach ($elements as $field => $value) {
            $this->hash->set($field, $value);
            $this->assertTrue($this->hash->contains($field));
            $this->assertEquals($this->hash->get($field), $value);
        }

        foreach ($elements as $field => $value) {
            $this->assertTrue($this->hash->exists($field));
        }

        foreach ($elements as $field => $value) {
            $this->hash->delete($field);
            $this->assertFalse($this->hash->exists($field));
        }
    }

    public function testElements()
    {
        $elements = $this->getRandomElements();

        foreach ($elements as $field => $value) {
            $this->hash->set($field, $value);
            $this->assertTrue($this->hash->contains($field));
        }

        $hashElements = $this->hash->elements();
        $this->assertEquals($hashElements, array_keys($elements));

        $hashElementsWithValues = $this->hash->elements(true);
        $this->assertEquals($hashElementsWithValues, $elements);

        return $elements;
    }

    public function testLenght()
    {
        $this->assertEquals(0, $this->hash->length());
        $elements = $this->testElements();

        $this->assertEquals(count($elements), $this->hash->length());
    }

    public function testIterator()
    {
        $elements = $this->testElements();
        foreach ($this->hash as $field) {
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
