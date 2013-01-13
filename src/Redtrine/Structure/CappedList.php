<?php

namespace Redtrine\Structure;

/**
 * Capped lists are fixed-length lists. Each time a new element is added
 * to the list, the list is trimmed to keep a fixed size.
 *
 * Reference:
 *    http://redis.io/topics/data-types#lists
 *    http://redis.io/commands/ltrim
 */
class CappedList extends LinkedList
{
    protected $length;

    public function __construct($name, $length)
    {
        parent::__construct($name);
        $this->length = $length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function leftPush($value)
    {
        $pipe = $this->client->pipeline();
        $pipe->lpush($this->key, $value);
        $pipe->ltrim($this->key, 0, $this->length - 1);
        $pipe->llen($this->key);
        $replies = $pipe->execute();

        return $replies[2];
    }

    public function rightPush($value)
    {
        $pipe = $this->client->pipeline();
        $pipe->rpush($this->key, $value);
        $pipe->ltrim($this->key, 0, $this->length - 1);
        $pipe->llen($this->key);
        $replies = $pipe->execute();

        return $replies[2];
    }

    public function insertBefore($pivot, $value)
    {
        $pipe = $this->client->pipeline();
        $pipe->linsert($this->key, 'BEFORE', $pivot, $value);
        $pipe->ltrim($this->key, 0, $this->length - 1);
        $pipe->llen($this->key);
        $replies = $pipe->execute();

        if ($replies[0] == -1) {
            return $replies[0];
        } else {
            return $replies[2];
        }
    }

    public function insertAfter($pivot, $value)
    {
        $pipe = $this->client->pipeline();
        $pipe->linsert($this->key, 'AFTER', $pivot, $value);
        $pipe->ltrim($this->key, 0, $this->length - 1);
        $pipe->llen($this->key);
        $replies = $pipe->execute();

        if ($replies[0] == -1) {
            return $replies[0];
        } else {
            return $replies[2];
        }
    }
}

