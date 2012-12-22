<?php

namespace Redtrine\Tests;

use Redtrine\Redtrine;

class RedtrineTest extends RedtrineTestCase
{
    /**
     * @covers Redtrine\Redtrine::getClient
     */
    public function testGetClient()
    {
        $this->assertNotNull($this->redtrine->getClient());
    }

    public function testCreate()
    {
        $set = $this->redtrine->create('Set', 'theNameOfTheSet');
        $this->assertInstanceOf('Redtrine\Structure\Set', $set);
    }

    public function testStructureKeyNaming()
    {
        $name = 'theNameOfTheSet';
        $structure = $this->redtrine->create('Set', $name);
        $this->assertEquals($name, $structure->getName());

        $name = array('the', 'name', 'of', 'the', 'set');
        $structure = $this->redtrine->create('Set', $name);
        $this->assertEquals('the:name:of:the:set', $structure->getName());
    }
}
