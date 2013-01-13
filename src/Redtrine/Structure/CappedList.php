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
        $replies = $this->client->pipeline(function($pipe) use ($value) {
            $pipe->lpush($this->key, $value);
            $pipe->ltrim($this->key, 0, $this->length - 1);
            $pipe->llen($this->key);
        });

        return $replies[2];
    }

    public function rightPush($value)
    {
        $replies = $this->client->pipeline(function($pipe) use ($value) {
            $pipe->rpush($this->key, $value);
            $pipe->ltrim($this->key, 0, $this->length - 1);
            $pipe->llen($this->key);
        });

        return $replies[2];
    }

    public function insertBefore($pivot, $value)
    {
        $replies = $this->client->pipeline(function($pipe) use ($pivot, $value) {
            $pipe->linsert($this->key, 'BEFORE', $pivot, $value);
            $pipe->ltrim($this->key, 0, $this->length - 1);
            $pipe->llen($this->key);
        });

        if ($replies[0] == -1) {
            return $replies[0];
        } else {
            return $replies[2];
        }
    }

    public function insertAfter($pivot, $value)
    {
        $replies = $this->client->pipeline(function($pipe) use ($pivot, $value) {
            $pipe->linsert($this->key, 'AFTER', $pivot, $value);
            $pipe->ltrim($this->key, 0, $this->length - 1);
            $pipe->llen($this->key);
        });

        if ($replies[0] == -1) {
            return $replies[0];
        } else {
            return $replies[2];
        }
    }
}

