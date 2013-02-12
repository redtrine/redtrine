<?php

namespace Redtrine\Structure;

class Queue extends LinkedList
{
    /**
     * @var bool $fifo
     */
    private $fifo;

    /**
     * @param string $name
     * @param bool $isFifo
     */
    public function __construct($name, $isFifo = true)
    {
        $this->fifo = $isFifo;
        parent::__construct($name);
    }

    /**
     * @return bool
     */
    public function isFifo()
    {
        return $this->fifo;
    }

    /**
     * @param bool $isFifo
     */
    public function setFifo($isFifo)
    {
        $this->fifo = $isFifo;
    }


    /**
     * @param mixed $value
     */
    public function enqueue($value)
    {
        $this->leftPush($value);
    }

    /**
     * @return mixed
     */
    public function dequeue()
    {
        return (true === $this->fifo) ? $this->rightPop() : $this->leftPop();
    }

    /**
     * Pops an element form the queue and atomically pushes into another queue
     *
     * @param Queue $target Popped element is pushed to this queue
     * 
     * @return mixed element being popped and pushed
     *
     * @link http://redis.io/commands/rpoplpush
     */
    public function dequeueEnqueue(Queue $target)
    {
        return $this->client->rpoplpush($this->getName(),$target->getName());
    }

    /**
     * Pops an element form the queue and atomically pushes into another queue
     * Blocks $timeout if there is no element to pop.
     *
     * @param Queue $target Popped element is pushed to this queue
     * @param int $timeout After $timeout seconds null is returned if queue is empty
     *
     * @return mixed element being popped and pushed, null on timeout
     *
     * @link http://redis.io/commands/brpoplpush
     */
    public function blockingDequeueEnqueue(Queue $target, $timeout)
    {
        return $this->client->brpoplpush($this->getName(),$target->getName(),$timeout);
    }
}