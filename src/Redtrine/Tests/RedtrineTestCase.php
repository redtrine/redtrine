<?php

namespace Redtrine\Tests;

use Redtrine\Redtrine;
use Predis\Client as Redis;

class RedtrineTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Redtrine
     */
    protected $redtrine;

    protected $redis;

    protected function setUp()
    {
        $this->redis = new Redis('tcp://127.0.0.1:6379');
        $this->redis->select(15);
        $this->redis->flushdb();

        $this->redtrine = new Redtrine();
        $this->redtrine->setClient($this->redis);
    }

    protected function tearDown()
    {
        unset($this->redis);
    }
}
