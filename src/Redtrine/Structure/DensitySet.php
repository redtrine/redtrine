<?php

namespace Redtrine\Structure;

class DensitySet extends Base
{
    /**
     * Increments one time the specified members score on dedicated sorted set
     * @see http://redis.io/commands/zincrby
     */
    public function add($member)
    {
        $this->client->zincrby($this->key, 1, $member);
    }

    /**
     * Decrements one time the specified members score on dedicated sorted set
     * Avoids negatives scores
     *
     * @param  string $member
     */
    public function rem($member)
    {
        $result = $this->client->zincrby($this->key, -1, $member);
        if ($result <= 0)
            $this->client->zremrangebyscore($this->key, '-inf', 0);
    }

    /**
     * Returns ranking of desired member
     *
     * @param string $member
     * @return int
     */
    public function rank($member)
    {
        return $this->client->zscore($this->key, $member);
    }

    /**
     * Returns lenght of sorted set
     *
     * @return int
     */
    public function length()
    {
        return $this->client->zcard($this->key);
    }

    /**
     * Alias of lenght
     *
     * @return int
     */
    public function count()
    {
        return $this->length();
    }

    /**
     * Removes sorted set
     */
    public function removeAll()
    {
        $this->client->del($this->key);
    }
}
