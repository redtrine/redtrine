<?php

namespace Redtrine\Structure;

/**
 * A circular list is a linked list that can be atomically rotated from left
 * to right (removing the tail and pushing it at head).
 */
class CircularList extends LinkedList
{
    /**
     * Atomically returns and removes the last element (tail) of the list
     * stored at source, and pushes the element at the first element (head).
     *
     * @link http://redis.io/commands/rpoplpush
     */
    public function rotate()
    {
        return $this->client->rpoplpush($this->key, $this->key);
    }

}
