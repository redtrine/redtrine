<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\KeyPair;
use Redtrine\Tests\RedtrineTestCase;

class KeyPairTest extends RedtrineTestCase
{
    /**
     * @var KeyPair
     */
    protected $keyPair;

    public function setUp()
    {
        parent::setUp();

        $this->keyPair = new KeyPair('keyPairName');
        $this->keyPair->setClient($this->getRedisClient());
        $this->keyPair->clear();
    }

    public function testAddValues()
    {
        $this->keyPair->add('a');
        $this->keyPair->add('b');

        $this->assertCount(2, $this->keyPair);
        $this->assertEquals(array(1, 2), $this->keyPair->ids());
        $this->assertEquals(array('a', 'b'), $this->keyPair->values());
    }
}