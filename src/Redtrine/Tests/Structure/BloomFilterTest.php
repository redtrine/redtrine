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
        // $this->set->setSize(2);
        // $this->set->setNumHashes(100000);

        $this->set->add('insertedKey');

        $this->assertFalse($this->set->exists('notExistent'));

    }

}