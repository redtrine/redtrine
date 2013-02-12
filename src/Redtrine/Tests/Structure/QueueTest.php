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
    private $otherQueue;

    public function setUp()
    {
        parent::setUp();
        $this->queue = new Queue('queueName');
        $this->queue->setClient($this->getRedisClient());

        $this->otherQueue = new Queue('otherQueueName');
        $this->otherQueue->setClient($this->getRedisClient());
    }

    public function tearDown()
    {
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

    public function testRpopLpush()
    {
        $this->loadValues();
        $poppedValue = $this->queue->dequeueEnqueue($this->otherQueue);
        $this->assertEquals($poppedValue, $this->otherQueue->dequeue());
    }

    public function testBrpopLpushWithValues()
    {
        $this->loadValues();
        $poppedValue = $this->queue->blockingDequeueEnqueue($this->otherQueue,1);
        $this->assertNotNull($poppedValue);
        $this->assertEquals($poppedValue, $this->otherQueue->dequeue());
    }

    public function testBrpopLpushTimeout()
    {
        $poppedValue = $this->queue->blockingDequeueEnqueue($this->otherQueue,1);
        $this->assertNull($poppedValue);
    }

    private function loadValues()
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->queue->enqueue($i);
        }
    }
}