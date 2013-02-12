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
        $this->add3values();
    }

    public function tearDown()
    {
        $this->keyPair->clear();
        parent::tearDown();
    }

    public function testAddingValuesIsOkAndAutoIncrementWorksAsExpected()
    {
        $this->assertCount(3, $this->keyPair);
        $this->assertEquals(array(1, 2, 3), $this->keyPair->ids());
        $this->assertEquals(array('a', 'b', 'c'), $this->keyPair->values());
    }

    public function testAddingValueAlreadyExistingReturnsOldIdAndIsNotInsertedAgain()
    {
        $this->assertEquals(2, $this->keyPair->add('b'));
        $this->assertCount(3, $this->keyPair);
    }

    public function testIdExistsMethod()
    {
        $this->assertTrue($this->keyPair->idExists(1));
        $this->assertFalse($this->keyPair->idExists(4));
    }

    public function testValueExistsMethod()
    {
        $this->assertTrue($this->keyPair->valueExists('a'));
        $this->assertFalse($this->keyPair->valueExists('z'));
    }

    public function testGetByIdWorks()
    {
        $this->assertEquals('b', $this->keyPair->getById(2));
    }

    public function testGetByIdsArrayWorks()
    {
        $this->assertEquals(array('a', 'b'), $this->keyPair->getById(array(1, 2)));
    }

    public function testGetWorks()
    {
        $this->assertEquals(2, $this->keyPair->get('b'));
    }

    public function testGetArrayWorks()
    {
        $this->assertEquals(array(1, 2), $this->keyPair->get(array('a', 'b')));
    }

    public function testDeleteByValueWorks()
    {
        $this->keyPair->delete('b');
        $this->assertCount(2, $this->keyPair);
        $this->assertEquals(array(1, 3), $this->keyPair->ids());
        $this->assertEquals(array('a', 'c'), $this->keyPair->values());
    }

    public function testDeleteByIdWorks()
    {
        $this->keyPair->deleteById(2);
        $this->assertCount(2, $this->keyPair);
        $this->assertEquals(array(1, 3), $this->keyPair->ids());
        $this->assertEquals(array('a', 'c'), $this->keyPair->values());
    }

    protected function add3values()
    {
        $this->keyPair->add('a');
        $this->keyPair->add('b');
        $this->keyPair->add('c');
    }
}