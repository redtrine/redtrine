<?php

namespace Redtrine\Structure;

class BitField extends Base
{
    /**
     * Get single bit
     *
     * @param string $item
     */
    public function get($item)
    {
        return $this->client->getbit($this->key, $item);
    }

    /**
     * set single bit
     *
     * @param string $item
     */
    public function set($item, $value)
    {
        $this->client->setbit($this->key, $item, $value);
    }
}