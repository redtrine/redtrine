<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\DensitySet;
use Redtrine\Tests\RedtrineTestCase;

class DensitySetTest extends RedtrineTestCase
{
    /**
     * @var SortedSet
     */
    protected $set;

    public function setUp()
    {
        parent::setUp();

        $this->set = new DensitySet('theNameOfTheDensitySet');
        $this->set->setClient($this->getRedisClient());
        $this->set->removeAll();
    }

    /**
     * @dataProvider getElementsToAdd
     */
    public function testAddItemsOnDensitySetIncrementsKeyScore($times, $expectedScore)
    {
        for ($i=0; $i < $times; $i++) {
            $this->set->add('key');
        }

        $this->assertEquals($this->set->rank('key'), $expectedScore);
    }


    public function getElementsToAdd()
    {
        return array(
            array(1, 1),
            array(3, 3),
            array(15, 15)
        );
    }

    /**
     * @dataProvider getElementsToRemove
     */
    public function testRemoveItemsOnDensitySetDecrementKeyScore($times, $expectedScore)
    {
        for ($i=0; $i < 15; $i++) {
            $this->set->add('key');
        }

        for ($i=0; $i < $times; $i++) {
            $this->set->rem('key');
        }

        $this->assertEquals($this->set->rank('key'), $expectedScore);
    }

    public function getElementsToRemove()
    {
        return array(
            array(1, 14),
            array(15, 0),
            array(30, 0)
        );
    }
}
