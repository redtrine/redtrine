<?php

namespace Redtrine\Structure;

class HashMap extends Hash implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Checks whether an offset exists.
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * Retrieve an offset.
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set offset.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset an offset.
     *
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
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

    /**
     * Retrieve an external iterator.
     *
     * @return Traversable An instance of an object implementing Iterator or Traversable.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements(true));
    }
}
