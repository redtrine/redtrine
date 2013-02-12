<?php

namespace Redtrine\Structure;

class KeyPair extends Base implements \Countable, \IteratorAggregate
{
    /**
     * @var string Redis Key containing reversed key values to provide O(1) lookup by both ID and value
     */
    private $idKey;

    /**
     * @var string Redis Key containing auto-increment ID for KeyPair instance
     */
    private $autoIncKey;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->idKey = $name . ':ids';
        $this->autoIncKey = $name . ':autoinc';
        parent::__construct($name);
    }

    /**
     * Adds value if it does not exist in the structure with an auto-increment ID associated
     * If value already exists, it just returns the key
     *
     * @param string $value
     * @return integer Auto-increment ID in the structure
     */
    public function add($value)
    {
        if ($id = $this->get($value)) {
            return $id;
        }

        $id = $this->client->incr($this->autoIncKey);

        $pipe = $this->client->pipeline();
        $pipe->multi();
        $pipe->hsetnx($this->idKey, $value, $id);
        $pipe->hsetnx($this->name, $id, $value);
        $pipe->exec();
        $pipe->execute();

        return $id;
    }

    /**
     * Lookup a unique value and get associated ID
     * @param mixed $value
     * @return mixed
     */
    public function get($value)
    {
        return is_array($value) ? $this->client->hmget($this->idKey, $value) : $this->client->hget($this->idKey, $value);
    }

    /**
     * Gets the value associated with the ID
     * @param mixed $id
     * @return mixed
     */
    public function getById($id)
    {
        return is_array($id) ? $this->client->hmget($this->name, $id) : $this->client->hget($this->name, $id);
    }

    /**
     * Gets the array of IDs
     * @return mixed
     */
    public function ids()
    {
        return $this->client->hkeys($this->name);
    }

    /**
     * Gets the array of values
     * @return mixed
     */
    public function values()
    {
        return $this->client->hvals($this->name);
    }

    /**
     * Checks if and id exists
     * @param integer $id
     * @return bool
     */
    public function idExists($id)
    {
        return $this->client->hexists($this->name, $id);
    }

    /**
     * Checks if a value exists in structure
     * @param string $value
     * @return bool
     */
    public function valueExists($value)
    {
        return $this->client->hexists($this->idKey, $value);
    }

    /**
     * Deletes pair from value
     * @param string $value
     */
    public function delete($value)
    {
        $id = $this->client->hget($this->idKey, $value);
        $this->clearPair($id, $value);
    }

    /**
     * Deletes pair from ID
     * @param int $id
     */
    public function deleteById($id)
    {
        $value = $this->client->hget($this->name, $id);
        $this->clearPair($id, $value);
    }

    /**
     * Actually deletes Key-Value pair in a Redis transaction
     * @param integer $id
     * @param string $value
     */
    private function clearPair($id, $value)
    {
        $pipe = $this->client->pipeline();
        $pipe->multi();
        $pipe->hdel($this->key, $id);
        $pipe->hdel($this->idKey, $value);
        $pipe->exec();
        $pipe->execute();
    }

    /**
     * Gets the number of unique values
     * @return integer
     */
    public function length()
    {
        return $this->client->hlen($this->name);
    }

    /**
     * Implementing IteratorAggregate interface
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_combine($this->ids(), $this->values()));
    }

    /**
     * Implementing Countable interface
     * @return int
     */
    public function count()
    {
        return $this->length();
    }

    /**
     * Clears all Redis keys associated with the structure
     */
    public function clear()
    {
        $this->client->del($this->autoIncKey);
        $this->client->del($this->idKey);
        $this->client->del($this->name);
    }
}