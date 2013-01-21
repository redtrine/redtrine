<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Tests\RedtrineTestCase;
use Redtrine\Structure\BitField;

class BitFieldTest extends RedtrineTestCase
{
    /**
     * @var bitField
     */
    protected $bit;

    public function setUp()
    {
        parent::setUp();

        $this->bit = new BitField('TestBitField');
        $this->bit->setClient($this->getRedisClient());
    }

    /**
     * @dataProvider bitValues
     */
    public function testToSetAndGetBitField($key, $value)
    {
        $this->bit->set($key, $value);

        $this->assertEquals($this->bit->get($key), $value);
    }

    public function bitValues()
    {
        return array(
            array(100,0),
            array(101,0),
            array(100,1),
            array(101,1),
        );
    }
}