<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Redtrine;
use Redtrine\Structure\Set;
use Redtrine\Tests\RedtrineTestCase;

class SetTest extends RedtrineTestCase
{
    /**
     * @var Set
     */
    protected $set;

    protected function setUp()
    {
        parent::setUp();
        $this->set = $this->redtrine->create('Set', 'theNameOfTheSet');
        $this->set->removeAll();
    }

    /**
     * @dataProvider getElements
     */
    public function testAdd($element)
    {
        $this->set->add($element);
        $this->assertTrue($this->set->contains($element));

        // Test with an array of elements.
        $elements = $this->getRandomElements();
        $this->set->removeAll();
        $this->set->add($elements);

        $this->assertCount(count($elements), $this->set);

    }

    /**
     * @dataProvider getElements
     */
    public function testRemove($element)
    {
        $this->set->add($element);
        $this->assertTrue($this->set->contains($element));

        $this->set->remove($element);
        $this->assertFalse($this->set->contains($element));
    }

    public function testExists()
    {
        $elements = $this->getRandomElements();
        foreach ($elements as $element) {
            $this->set->add($element);
            $this->assertTrue($this->set->contains($element));
        }

        foreach ($elements as $element) {
            $this->assertTrue($this->set->exists($element));
        }

        foreach ($elements as $element) {
            $this->set->remove($element);
            $this->assertFalse($this->set->exists($element));
        }
    }

    public function testElements()
    {
        $elements = $this->getRandomElements();

        foreach ($elements as $element) {
            $this->set->add($element);
            $this->assertTrue($this->set->contains($element));
        }

        $setElements = array_values($this->set->elements());
        sort($setElements);

        $elements = array_values(array_unique($elements));
        sort($elements);

        $this->assertEquals($setElements, $elements);

        return $elements;
    }

    public function testLenght()
    {
        $this->assertEquals(0, $this->set->length());
        $elements = $this->testElements();

        $this->assertEquals(count($elements), $this->set->length());
    }

    public function testUnion()
    {
        $a = $this->redtrine->create('Set', 'setA');
        $a->add(array(1, 2, 3, 4));
        $b = $this->redtrine->create('Set', 'setB');
        $b->add(array(3, 4, 5, 6, 7, 8));

        $this->assertEquals($a->union($b), array(1, 2, 3, 4, 5, 6, 7, 8));
    }

    public function testUnionStore()
    {
        $a = $this->redtrine->create('Set', 'setA');
        $a->add(array(1, 2, 3, 4));
        $b = $this->redtrine->create('Set', 'setB');
        $b->add(array(3, 4, 5, 6, 7, 8));

        $destination = 'setDestination';
        $total = $a->unionStore($destination, $b);
        $this->assertEquals(8 , $total);

        $c = $this->redtrine->create('Set', $destination);

        $this->assertEquals($c->elements(), array(1, 2, 3, 4, 5, 6, 7, 8));
    }

    public function testIterator()
    {
        $elements = $this->testElements();
        foreach ($this->set as $element) {
            $this->assertContains($element, $elements);
        }
    }

    public function getElements()
    {
        $result = array();
        foreach ($this->getRandomElements() as $element) {
            $result[] = array($element);
        }

        return $result;
    }

    public function getRandomElements()
    {
        $result = array();
        $total = 20;
        for ($i = 0; $i < $total; $i++) {
            $result[] = md5(uniqid(rand(), true));
        }

        return $result;
    }
}
