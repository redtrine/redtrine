<?php

namespace Redtrine\Structure;

class LinkedList extends Base implements \IteratorAggregate, \Countable
{
    public function leftPush($value)
    {
        $this->client->lpush($this->key, $value);
    }

    public function rightPush($value)
    {
        $this->client->rpush($this->key, $value);
    }

    /**
     * Inserts value in the list befor ethe reference value pivot.
     */
    public function insertBefore($pivot, $value)
    {
        return $this->client->linsert($this->key, 'BEFORE', $pivot, $value);
    }

    /**
     * Inserts value in the list after the reference value pivot.
     */
    public function insertAfter($pivot, $value)
    {
        return $this->client->linsert($this->key, 'AFTER', $pivot, $value);
    }

    /**
     * Sets the list element at the specified index.
     */
    public function set($index, $element)
    {
        return $this->client->lset($this->key, $index, $element);
    }

    /**
     * Returns the element at the specified position in the list.
     * The index is zero-based, so 0 means the first element, 1 the second
     * element and so on. Negative indices can be used to designate elements
     * starting at the tail of the list. Here, -1 means the last element,
     * -2 means the penultimate and so forth.
     *
     * @see http://redis.io/commands/lindex
     */
    public function get($index)
    {
        return $this->client->lindex($this->name, $index);
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
        $this->client->rpop($this->key);
    }

    /**
     * Returns the elements of the list as an array..
     */
    public function elements($start = 0, $stop = -1)
    {
        return $this->client->lrange($this->key, $start, $stop);
    }

    /**
     * Returns a range of list elements.
     */
    public function range($start = 0, $stop = -1)
    {
        return $this->client->lrange($this->key, $start, $stop);
    }

    /**
     * Returns the length of the list.
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
        return new ArrayIterator($this->elements());
    }

    public function removeAll()
    {
        $this->client->del($this->key);
    }

    /**
     * Removes the first count occurrences of elements equal to value from
     * the list.
     *
     * @see http://redis.io/commands/lrem
     */
    public function remove($count, $value)
    {
        $this->client->rem($this->key, $count, $value);
    }

    /**
     * Trim the list so that it will contain only the specified range of elements
     * specified.  Both start and stop are zero-based indexes, where 0 is the
     * first element of the list (the head), 1 the next element and so on.
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
        $this->trim(0, $length - 1);
    }

    public function head()
    {
        return $this->range(0, 0);
    }

    public function tail()
    {
        return $this->range(-1, -1);
    }
}
