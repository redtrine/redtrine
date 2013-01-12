<?php

namespace Redtrine\Structure;

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
