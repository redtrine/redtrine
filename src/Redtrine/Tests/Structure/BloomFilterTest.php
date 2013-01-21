<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\BloomFilter;
use Redtrine\Tests\RedtrineTestCase;

class BloomFilterTest extends RedtrineTestCase
{
    /**
     * @var SortedSet
     */
    protected $set;

    public function setUp()
    {
        parent::setUp();

        $this->set = new BloomFilter('TestBloomFilter',100, 2);
        $this->set->setClient($this->getRedisClient());
        $this->set->reset();
    }

    public function testToAddOneItemOnASpreadBloomFilterNotExists()
    {
        $this->set->add('insertedKey');

        $this->assertFalse($this->set->exists('notExistentKey'));
    }

    public function testToAddOneItemOnASpreadBloomFilterMayExists()
    {
        $this->set->add('newKey');

        $this->assertTrue($this->set->exists('newKey'));
    }

    public function testOptimalNumberOfHashesCalculation()
    {
        $this->assertEquals($this->set->getOptimalNumberOfHashes(1000000), 1);
    }

}