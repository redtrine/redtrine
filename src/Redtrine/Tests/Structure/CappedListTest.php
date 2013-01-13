<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\CappedList;
use Redtrine\Tests\RedtrineTestCase;

class CappedListTest extends RedtrineTestCase
{
    /**
     * @var CappedList
     */
    protected $list;

    public function setUp()
    {
        parent::setUp();

        $this->list = new CappedList('listName', 10);
        $this->list->setClient($this->getRedisClient());
        $this->list->removeAll();
    }

    public function testLeftPush()
    {
        $this->list->setLength(2);
        $this->list->leftPush(array(1, 2, 3));

        $this->assertEquals(2, $this->list->length());
    }

    public function testRightPush()
    {
        $this->list->setLength(2);
        $this->list->rightPush(array(1, 2, 3));

        $this->assertEquals(2, $this->list->length());
    }

    /**
     * @dataProvider insertBeforeDataProvider
     */
    public function testInsertBefore($values, $length, $pivot, $value, $return, $expectedValues)
    {
        $this->list->setLength($length);
        if (count($values)) {
            $this->list->rightPush($values);
        }

        $this->assertEquals($return, $this->list->insertBefore($pivot, $value));
        $this->assertEquals($expectedValues, $this->list->elements());
    }

    public function insertBeforeDataProvider()
    {
        return array(
            array(array(), 3, 10, 123, 0, array()),
            array(array(1, 2, 3), 3, 10, 123, -1, array(1, 2, 3)),
            array(array(1, 3, 4, 5), 3, 3, 2, 3, array(1, 2, 3)),
            array(array(1, 3, 4, 5), 10, 3, 2, 5, array(1, 2, 3, 4, 5)),

        );
    }

    /**
     * @dataProvider insertAfterDataProvider
     */
    public function testInsertAfter($values, $length, $pivot, $value, $return, $expectedValues)
    {
        $this->list->setLength($length);
        if (count($values)) {
            $this->list->rightPush($values);
        }

        $this->assertEquals($return, $this->list->insertAfter($pivot, $value));
        $this->assertEquals($expectedValues, $this->list->elements());
    }

    public function insertAfterDataProvider()
    {
        return array(
            array(array(), 3, 10, 123, 0, array()),
            array(array(1, 2, 3), 3, 10, 123, -1, array(1, 2, 3)),
            array(array(1, 2, 4, 5), 3, 2, 3, 3, array(1, 2, 3)),
            array(array(1, 2, 4, 5), 10, 2, 3, 5, array(1, 2, 3, 4, 5)),

        );
    }
}
