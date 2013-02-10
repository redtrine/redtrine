<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Tests\RedtrineTestCase;
use Redtrine\Structure\RateLimit;

class RateLimitTest extends RedtrineTestCase
{
    protected $rateLimit;

    public function setUp()
    {
        parent::setUp();

        $this->rateLimit = new RateLimit('TestRateLimit');
        $this->rateLimit->setClient($this->getRedisClient());
    }

    /**
     * @dataProvider dataItemProvider
     */
    public function testToAddItemsOnBucket($subject, $total)
    {
        $this->rateLimit->add($subject);
        $reflectionObj = new \ReflectionObject($this->rateLimit);
        $reflectionMethod = $reflectionObj->getMethod('getBucket');
        $reflectionMethod->setAccessible(true);

        $bucket = $reflectionMethod->invoke($this->rateLimit, time());
        $subject = (is_array($subject))? $subject: array($subject);
        foreach (array_unique($subject) as $item) {
            $result = $this->redis->hget('TestRateLimit:'.$item, $bucket);
            $this->assertEquals($result, $total[$item]);
        }
    }

    public function dataItemProvider()
    {
        return array(
            array('item1', array('item1'=>1)),
            array(array('item1', 'item1', 'item1'),  array('item1'=>3)),
            array(array('item1', 'item1', 'item2'),  array('item1'=>2, 'item2'=>1)),
            array(array('item1', 'item2', 'item2', 'item3'),  array('item1'=>1, 'item2'=>2, 'item3'=>1))
        );
    }

    /**
     * @dataProvider dataItemCountProvider
     */
    public function testCountToGetTimesActionIsPerformed($subject, $interval, $total)
    {
        $this->rateLimit->add($subject);
        $reflectionObj = new \ReflectionObject($this->rateLimit);
        $reflectionMethod = $reflectionObj->getMethod('getBucket');
        $reflectionMethod->setAccessible(true);

        $bucket = $reflectionMethod->invoke($this->rateLimit, time());

        $subject = (is_array($subject))? $subject: array($subject);
        foreach (array_unique($subject) as $item) {
            $this->assertEquals($this->rateLimit->count($item, $interval), $total[$item]);
        }
    }

    public function dataItemCountProvider()
    {
        return array(
            array('item1', 5, array('item1'=>1)),
            array(array('item1', 'item1', 'item1'), 5, array('item1'=>3)),
            array(array('item1', 'item1', 'item2'), 5, array('item1'=>2, 'item2'=>1)),
            array(array('item1', 'item2', 'item2', 'item3'), 5, array('item1'=>1, 'item2'=>2, 'item3'=>1))
        );
    }

    /**
     *
     * @dataProvider dataItemCountProvider
     */
    public function testAddSingleItemAndCount($subject, $interval, $total)
    {
        $result = $this->rateLimit->addCount($subject, $interval);

        $subject = (is_array($subject))? $subject: array($subject);
        foreach (array_unique($subject) as $item) {
            $this->assertEquals($this->rateLimit->count($item, $interval), $total[$item]);
        }
    }

    public function testAddAndCountItemsOnDifferentTimeValues()
    {
        $this->rateLimit->add(array('item1', 'item1', 'item2'), time()-10);
        $this->rateLimit->add('item1', time()-5);
        $result = $this->rateLimit->addCount(array('item1', 'item2'), 15);

        $this->assertEquals($result['item1'] , 4);
        $this->assertEquals($result['item2'] , 2);
    }

    public function testResetRateLimitDestroysStructure()
    {
        $this->rateLimit->reset();
        $this->assertEquals($this->redis->hgetAll('TestRateLimit'), array());
    }

    /**
     *
     * @dataProvider getBucketValues
     * @return [type] [description]
     */
    public function testGetBucketOnMultipleValues($time, $bucket)
    {
        $reflectionObj = new \ReflectionObject($this->rateLimit);
        $reflectionMethod = $reflectionObj->getMethod('getBucket');
        $reflectionMethod->setAccessible(true);

        $this->assertEquals($reflectionMethod->invoke($this->rateLimit, $time), $bucket);
    }

    /**
     * Total buckets in example 120
     * Defauk¡lt bucketspan  600
     * bucketInterval  5
     */
    public function getBucketValues()
    {
        return array(
            array(15000000,0),
            array(15000005,1),
            array(15000599,119)
        );
    }
}