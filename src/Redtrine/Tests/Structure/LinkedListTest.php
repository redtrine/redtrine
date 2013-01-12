<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\LinkedList;
use Redtrine\Tests\RedtrineTestCase;

class LinkedListTest extends RedtrineTestCase
{
    /**
     * @var LinkedList
     */
    protected $list;

    public function setUp()
    {
        parent::setUp();

        $this->list = new LinkedList('listName');
        $this->list->setClient($this->getRedisClient());
        $this->list->removeAll();
    }

    /**
     * @dataProvider singleValueProvider
     */
    public function testLeftPushWithSingleValue($value)
    {
        $this->list->leftPush($value);
        $this->assertEquals($value, $this->list->get(0));
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testLeftPushWithMultipleValues($values)
    {
        $this->list->leftPush($values);
        $this->assertEquals(array_reverse($values), $this->list->range(0, count($values) - 1));
    }

    /**
     * @dataProvider singleValueProvider
     */
    public function testRightPushWithSingleValue($value)
    {
        $this->list->rightPush($value);
        $this->assertEquals($value, $this->list->get($this->list->length() - 1));
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testRightPushWithMultipleValues($values)
    {
        $this->list->rightPush($values);
        $this->assertEquals($values, $this->list->range(0, count($values) - 1));
    }

    public function testSet()
    {
        $this->populateList();
        $value = 'set' . rand();

        $this->list->set(5, $value);
        $this->assertEquals($value, $this->list->get(5));
    }

    public function testInsertBefore()
    {
        $this->populateList();
        $value = rand();
        $pivot = $this->list->get(5);

        $this->list->insertBefore($pivot, $value);
        $this->assertEquals($value, $this->list->get(5));
    }

    public function testInsertAfter()
    {
        $this->populateList();
        $value = rand();
        $pivot = $this->list->get(5);

        $this->list->insertAfter($pivot, $value);
        $this->assertEquals($value, $this->list->get(6));
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testGet($values)
    {
        $this->list->rightPush($values);

        foreach ($values as $pos => $value) {
            $this->assertEquals($value, $this->list->get($pos));
        }
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testLeftPop($values)
    {
        $this->list->rightPush($values);

        foreach ($values as $pos => $value) {
            $this->assertEquals($value, $this->list->leftPop());
        }
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testRightPop($values)
    {
        $this->list->leftPush($values);

        foreach ($values as $pos => $value) {
            $this->assertEquals($value, $this->list->RightPop());
        }
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testRange($values)
    {
        $this->list->rightPush($values);

        $this->assertEquals($values, $this->list->range(0, count($values) - 1));
        $this->assertEquals(array_slice($values, 0, count($values) - 2), $this->list->range(0, -3));
        $this->assertEquals(array_slice($values, 1, count($values) - 1), $this->list->range(1, -1));
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testElements($values)
    {
        $this->list->rightPush($values);
        $this->assertEquals($values, $this->list->elements());
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testLength($values)
    {
        $this->list->rightPush($values);
        $this->assertEquals(count($values), $this->list->length());
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testCount($values)
    {
        $this->list->rightPush($values);
        $this->assertEquals(count($values), $this->list->count());
        $this->assertEquals(count($values), count($this->list));
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testGetIterator($values)
    {
        $this->list->rightPush($values);

        foreach ($this->list as $key => $value) {
            $this->assertEquals($values[$key], $value);
        }
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testRemoveAll($values)
    {
        $this->list->rightPush($values);
        $this->list->removeAll();

        $this->assertEquals(0, count($this->list));
    }

    /**
     * @dataProvider removeDataProvider
     */
    public function testRemove($values, $count, $value, $expected, $return)
    {
        $this->list->rightPush($values);

        $this->assertEquals($return, $this->list->remove($count, $value));
        $this->assertEquals($expected, $this->list->elements());
    }

    public function removeDataProvider()
    {
        return array(
            array(array(1, 10, 2, 10, 3, 10, 4, 10), 0, 10, array(1, 2, 3, 4), 4),
            array(array(1, 10, 2, 10, 3, 10, 4, 10), 2, 10, array(1, 2, 3, 10, 4, 10), 2),
            array(array(1, 10, 2, 10, 3, 10, 4, 10), -2, 10, array(1, 10, 2, 10, 3, 4), 2),
            array(array(1, 10, 2,  3, 4), 2, 10, array(1, 2, 3,  4), 1),
            array(array(1, 2,  3, 4, 5, 6, 7, 8), 0, 10, array(1, 2,  3, 4, 5, 6, 7, 8), 0),
        );
    }

    /**
     * @dataProvider trimDataProvider
     */
    public function testTrim($values, $start, $stop, $expected)
    {
        $this->list->rightPush($values);
        $this->list->trim($start, $stop);

        $this->assertEquals($expected, $this->list->elements());
    }

    public function trimDataProvider()
    {
        return array(
            array(array(1, 2, 3, 4, 5), 0, 1, array(1, 2)),
            array(array(1, 2, 3, 4, 5), 0, 10, array(1, 2, 3, 4, 5)),
            array(array(1, 2, 3, 4, 5), 0, -2, array(1, 2, 3, 4)),
            array(array(1, 2, 3, 4, 5), 0, -10, array()),
            array(array(1, 2, 3, 4, 5), -3, -1, array(3, 4, 5)),
        );
    }

    /**
     * @dataProvider capDataProvider
     */
    public function testCap($values, $length, $expectedLength)
    {
        $this->list->rightPush($values);

        $this->list->cap($length);
        $this->assertEquals(array_slice($values, 0, $length), $this->list->elements());
        $this->assertEquals($expectedLength, $this->list->length());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCapThrowExceptionWithLengthZero()
    {
        $this->list->cap(0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCapThrowExceptionWithNegativeLength()
    {
        $this->list->cap(-10);
    }

    public function capDataProvider()
    {
        return array(
            array($this->getRandomValues(10), 20, 10),
            array($this->getRandomValues(10), 10, 10),
        );
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testHead($values)
    {
        $this->list->rightPush($values);

        $this->assertEquals(array_shift($values), $this->list->head());
    }

    /**
     * @dataProvider multipleValuesProvider
     */
    public function testTail($values)
    {
        $this->list->rightPush($values);

        $this->assertEquals(array_pop($values), $this->list->tail());
    }

    public function singleValueProvider()
    {
        $result = array();
        foreach ($this->getRandomValues() as $value) {
            $result[] = array($value);
        }

        return $result;
    }

    public function multipleValuesProvider()
    {
        return array(
            array(array(1, 2, 3, 4)),
            array(array('a', 'b', 'c')),
            array(array(12345, 'abcd')),
            array(array(1, 'a', 2, 'b', 3, 'c', 4, 'd', 5, 'e')),
        );
    }

    protected function getRandomValues($total = 20)
    {
        $result = array();
        for ($i = 0; $i < $total; $i++) {
            $result[] = md5(uniqid(rand(), true));
        }

        return $result;
    }

    protected function populateList()
    {
        foreach ($this->getRandomValues() as $value) {
            $this->list->leftPush($value);
        }
    }
}
