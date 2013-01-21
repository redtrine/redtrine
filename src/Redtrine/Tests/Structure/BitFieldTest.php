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
    public function testToSetAndGetBitField($value)
    {
        $this->bit->set(100, $value);

        $this->assertEquals($this->bit->get(100), $value);
    }

    public function bitValues()
    {
        return array(
            array(0),
            array(1)
        );
    }
}