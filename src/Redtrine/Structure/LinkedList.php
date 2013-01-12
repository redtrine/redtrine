<?php

namespace Redtrine\Structure;

class LinkedList extends Base implements \IteratorAggregate, \Countable
{
    /**
     * Prepend one or multiple values to a list.
     *
     * @param mixed|array $value
     * @return int The length of the list after the push operation.
     *
     * @link http://redis.io/commands/lset
     */
    public function leftPush($value)
    {
        return $this->client->lpush($this->key, $value);
    }

    /**
      * Append one or multiple values to a list.
      *
      * @param mixed|array $value
      * @return int The length of the list after the push operation.
     *
      * @link http://redis.io/commands/rpush
      */
    public function rightPush($value)
    {
        return $this->client->rpush($this->key, $value);
    }

    /**
     * Sets the list element at the specified index.
     *
     * @link http://redis.io/commands/lset
     */
    public function set($index, $element)
    {
        return $this->client->lset($this->key, $index, $element);
    }

    /**
     * Inserts value in the list before the reference value pivot.
     *
     * @param $pivot
     * @param $value
     *
     * @link http://redis.io/commands/linsert
     */
    public function insertBefore($pivot, $value)
    {
        return $this->client->linsert($this->key, 'BEFORE', $pivot, $value);
    }

    /**
     * Inserts value in the list after the reference value pivot.
     *
     * @param $pivot
     * @param $value
     *
     * @link http://redis.io/commands/linsert
     */
    public function insertAfter($pivot, $value)
    {
        return $this->client->linsert($this->key, 'AFTER', $pivot, $value);
    }

    /**
     * Returns the element at the specified position in the list.
     *
     * @link http://redis.io/commands/lindex
     */
    public function get($index)
    {
        return $this->client->lindex($this->key, $index);
    }

    /**
     * Removes and returns the first element of the list.
     */
    public function leftPop()
    {
        return $this->client->lpop($this->key);
    }

    public function rightPop()
    {
        return $this->client->rpop($this->key);
    }

    /**
     * Returns a range of list elements.
     *
     * @link http://redis.io/commands/lrange
     */
    public function range($start = 0, $stop = -1)
    {
        return $this->client->lrange($this->key, $start, $stop);
    }

    /**
     * Returns the elements of the list as an array.
     */
    public function elements()
    {
        return $this->range(0, -1);
    }

    /**
     * Returns the length of the list.
     *
     * @link http://redis.io/commands/lrange
     */
    public function length()
    {
        return $this->client->llen($this->key);
    }

    /**
     * Count the elements in the list.
     */
    public function count()
    {
        return $this->length();
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->elements());
    }

    public function removeAll()
    {
        $this->client->del($this->key);
    }

    /**
     * Removes the first count occurrences of elements equal to value from
     * the list.
     *
     * count > 0: Remove elements equal to value moving from head to tail.
     * count < 0: Remove elements equal to value moving from tail to head.
     * count = 0: Remove all elements equal to value.
     *
     * @param int   $count
     * @param mixed $value
     * @return int The number of removed elements.
     *
     * @see http://redis.io/commands/lrem
     */
    public function remove($count, $value)
    {
        return $this->client->lrem($this->key, $count, $value);
    }

    /**
     * Trim the list so that it will contain only the specified range of elements
     * specified.
     *
     * @see http://redis.io/commands/ltrim
     */
    public function trim($start, $stop)
    {
        $this->client->ltrim($this->key, $start, $stop);
    }

    /**
     * Cap the list to a specific length.
     */
    public function cap($length)
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Length must be a positive integer.');
        }

        $this->trim(0, $length - 1);
    }

    /**
     * Returns the element on the head of the list.
     *
     * @return mixed
     */
    public function head()
    {
        return $this->get(0);
    }

    /**
     * Returns the element on the tail of the list.
     *
     * @return mixed
     */
    public function tail()
    {
        return $this->get(-1);
    }
}
