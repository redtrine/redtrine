<?php

namespace Redtrine\Structure;

class Cache extends Base
{
    /**
     * Get single value from Redis cache
     */
    public function get()
    {
        return $this->client->get($this->key);
    }

    /**
     * Sets value using Redis as a cache
     * Expires param is optional, and must be set in seconds
     *
     * @param string $value
     * @param int $expire
     */
    public function set($value, $expire = 0)
    {
        ($expire > 0) ? $this->client->setex($this->key, $expire, $value) : $this->client->set($this->key, $value);
    }
}
