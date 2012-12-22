<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Redtrine;
use Redtrine\Structure\Set;
use Redtrine\Tests\RedtrineTestCase;

class HashTest extends RedtrineTestCase
{
    /**
     * @var Set
     */
    protected $set;

    protected function setUp()
    {
        parent::setUp();
        $this->hash = $this->redtrine->create('Hash', 'theNameOfTheHash');
        $this->hash->removeAll();
    }

    /**
     * @dataProvider getElements
     */
    public function testAdd($field, $value)
    {
        $this->hash->add($field, $value);
        $this->assertTrue($this->hash->contains($field));
    }

    /**
     * @dataProvider getElements
     */
    public function testGet($field, $value)
    {
        $this->hash->add($field, $value);
        $this->assertTrue($this->hash->contains($field));
        $this->assertEquals($this->hash->get($field), $value);
    }

    /**
     * @dataProvider getElements
     */
    public function testRemove($field, $value)
    {
        $this->hash->add($field, $value);
        $this->assertTrue($this->hash->contains($field));
        $this->assertEquals($this->hash->get($field), $value);

        $this->hash->remove($field);
        $this->assertFalse($this->hash->contains($field));
        $this->assertNull($this->hash->get($field));
    }

    public function testExists()
    {
        $elements = $this->getRandomElements();
        foreach ($elements as $field => $value) {
            $this->hash->add($field, $value);
            $this->assertTrue($this->hash->contains($field));
            $this->assertEquals($this->hash->get($field), $value);
        }

        foreach ($elements as $field => $value) {
            $this->assertTrue($this->hash->exists($field));
        }

        foreach ($elements as $field => $value) {
            $this->hash->remove($field);
            $this->assertFalse($this->hash->exists($field));
        }
    }

    public function testElements()
    {
        $elements = $this->getRandomElements();

        foreach ($elements as $field => $value) {
            $this->hash->add($field, $value);
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
