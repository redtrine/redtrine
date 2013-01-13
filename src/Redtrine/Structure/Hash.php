<?php

namespace Redtrine\Structure;

use IteratorAggregate,
    ArrayIterator,
    Countable;

class Hash extends Base implements IteratorAggregate, Countable
{
    /**
     * Get an array of hash keys.
     *
     * @return array
     */
    public function keys()
    {
        return $this->client->hkeys($this->key);
    }

    /**
     * Get an array of hash values.
     *
     * @return array
     */
    public function values()
    {
        return $this->client->hvals($this->key);
    }

    /**
     * Sets one or more key/value pairs
     *
     * @param $field
     * @param $value
     */
    public function set($field, $value = null)
    {
        if (is_array($field)) {
            $this->client->hmset($this->key, $field);
        } else {
            $this->client->hset($this->key, $field, $value);
        }
    }

    /**
     * Get the value of a field into the hash
     *
     * @param $field
     * @return mixed
     */
    public function get($field)
    {
        return $this->client->hget($this->key, $field);
    }

    /**
     * Delete one or more hash fields.
     *
     * @param $field
     * @return int The number of fields that were removed from the hash,
     *             not including specified but non existing fields.
     *
     * @see http://redis.io/commands/hdel
     */
    public function delete($field)
    {
        return $this->client->hdel($this->key, $field);
    }

    /**
     * Check whether a field exists in the hash.
     *
     * @param $element
     * @return boolean
     */
    public function exists($field)
    {
        return $this->client->hexists($this->key, $field);
    }

    /**
     * Check whether a field exists in the hash.
     *
     * @param $element
     * @return boolean
     */
    public function contains($field)
    {
        return $this->exists($field);
    }

    /**
     * Get an array of fields stored in the hash.
     *
     * @return mixed
     */
    public function elements($withValues = false)
    {
        return $withValues ?
            $this->client->hgetall($this->key) :
            $this->client->hkeys($this->key);
    }

    /**
     * Returns the hash cardinality (number of fields) of the hash.
     *
     * @return int
     */
    public function length()
    {
        return $this->client->hlen($this->key);
    }

    /**
     * Count the elements in the object.
     *
     * @return int
     */
    public function count()
    {
        return $this->length();
    }

    public function removeAll()
    {
        $this->client->del($this->key);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->elements(true));
    }

}
