<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\Queue;
use Redtrine\Tests\RedtrineTestCase;

class QueueTest extends RedtrineTestCase
{
    /**
     * @var Queue
     */
    private $queue;

    public function setUp()
    {
        parent::setUp();
        $this->queue = new Queue('queueName');
        $this->queue->setClient($this->getRedisClient());
    }

    public function tearDown()
    {
        $this->queue->removeAll();
        parent::tearDown();
    }

    public function testEnqueueWorks()
    {
        $this->loadValues();
        $this->assertCount(3, $this->queue);
    }

    public function testFifoQueue()
    {
        $this->loadValues();
        $this->assertEquals(1, $this->queue->dequeue());
    }

    public function testLifoQueue()
    {
        $this->queue->setFifo(false);
        $this->loadValues();
        $this->assertEquals(3, $this->queue->dequeue());
    }

    private function loadValues()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->queue->enqueue($i);
        }
    }
}