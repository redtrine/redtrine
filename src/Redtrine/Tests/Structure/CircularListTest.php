<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\CircularList;
use Redtrine\Tests\RedtrineTestCase;

class CircularListTest extends RedtrineTestCase
{
    /**
     * @var CircularList
     */
    protected $list;

    public function setUp()
    {
        parent::setUp();

        $this->list = new CircularList('listName');
        $this->list->setClient($this->getRedisClient());
        $this->list->removeAll();
    }

    /**
     * @dataProvider rotateDataProvider
     */
    public function testRotate($values, $expected)
    {
        if (count($values)) {
            $this->list->rightPush($values);
        }

        $this->assertEquals($this->list->tail(), $this->list->rotate());
        $this->assertEquals($expected, $this->list->elements());
    }

    public function rotateDataProvider()
    {
        return array(
            array(array(), array()),
            array(array(1000), array(1000)),
            array(array(1, 2, 3), array(3, 1, 2)),
            array(array(1, 2, 3, 4, 5), array(5, 1, 2, 3, 4)),
        );
    }
}
