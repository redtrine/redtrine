<?php

namespace Redtrine\Structure;

class KeyPair extends Base implements \Countable, \IteratorAggregate
{
    private $idKey;

    private $lockKey;

    private $autoIncKey;

    private static $lockExpire = 5;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->idKey = $name . ':ids';
        $this->lockKey = $name . ':lock';
        $this->autoIncKey = $name . ':autoinc';
        parent::__construct($name);
    }

    public function add($value)
    {
        /**
         * If value exists... return Id!
         */
        if ($id = $this->get($value)) {
            return $id;
        }

        $this->client->setnx($this->lockKey, static::$lockExpire);
        $id = $this->client->incr($this->autoIncKey);

        $this->client->hset($this->idKey, $value, $id);
        $this->client->hset($this->name, $id, $value);
        $this->client->del($this->lockKey);
    }

    public function get($value)
    {
        return is_array($value) ? $this->client->hmget($this->name, $value) : $this->client->hget($this->name, $value);
    }

    public function ids()
    {
        return $this->client->hkeys($this->name);
    }

    public function values()
    {
        return $this->client->hvals($this->name);
    }

    public function idExists($id)
    {
        return $this->client->hexists($this->name, $id);
    }

    public function valueExists($value)
    {
        return $this->client->hexists($this->idKey, $value);
    }

    public function length()
    {
        return $this->client->hlen($this->key);
    }

    /**
     * @todo Check this...
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_combine($this->ids(), $this->values()));
    }

    public function count()
    {
        return $this->length();
    }

    public function clear()
    {
        $this->client->del($this->autoIncKey);
        $this->client->del($this->idKey);
        $this->client->del($this->name);
    }
}