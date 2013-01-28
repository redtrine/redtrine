<?php

namespace Redtrine\Structure;

class SortedSet extends Base implements \IteratorAggregate, \Countable
{
    /**
     * Adds the specified members with the specified scores to the sorted set.
     * @see http://redis.io/commands/zadd
     */
    public function add($member, $score = 0)
    {
        $this->client->zadd($this->key, $score, $member);
    }

    /**
     * Remove a member from the sorted set.
     * @see http://redis.io/commands/zrem
     */
    public function remove($member)
    {
        $this->client->zrem($this->key, $member);
    }

    /**
     * Check whether a mmeber exists in the set.
     * @see http://redis.io/commands/zscore
     */
    public function exists($member)
    {
        return null !== $this->client->zscore($this->key, $member);
    }

    /**
     * Check whether a member exists in the set.
     */
    public function contains($member)
    {
        return $this->exists($member);
    }

    /**
     * Get an array of elements stored in the set.
     */
    public function elements()
    {
        return $this->client->zrange($this->key, 0, -1);
    }

    /**
     * Returns the rank of the member in the sorted set, with the
     * scores ordered from low to high. The rank (or index) is 0-based,
     * which means that the member with the lowest score has rank 0.
     *
     * @see http://redis.io/commands/zrank
     */
    public function rank($member)
    {
        return $this->client->zrank($this->key, $member);
    }

    /**
     * Returns the score of the member in the sorted set.
     *
     * @see http://redis.io/commands/zscore
     */
    public function score($member)
    {
        return $this->client->zscore($this->key, $member);
    }

    public function range($start = 0, $stop = -1)
    {
        return $this->client->zrange($this->key, $start, $stop);
    }

    public function rangeWithScores($start = 0, $stop = -1)
    {
        return $this->normalizeScores($this->client->zrange($this->key, $start, $stop, 'WITHSCORES'));
    }

    public function rangeByScore($min, $max, $offset = null, $count = null)
    {
        if (isset($offset) && null === $count) {
            throw new \InvalidArgumentException('Count should not be null if an offset is specified.');
        }

        if (isset($offset) && isset($count)) {
            return $this->client->zrangebyscore($this->key, $min, $max, 'LIMIT ' . $offset . ' ' . $count);

        } else {
            return $this->client->zrangebyscore($this->key, $min, $max);
        }

        return $this->client->zrangebyscore($this->key, $min, $max, $limit);
    }


    public function reverseRange($start = 0, $stop = -1)
    {
        return $this->client->zrevrange($this->key, $start, $stop);
    }

    public function reverseRangeWithScores($start = 0, $stop = -1)
    {
        return $this->normalizeScores($this->client->zrevrange($this->key, $start, $stop, 'WITHSCORES'));
    }

    /**
     * Returns the set cardinality (number of elements) of the set.
     *
     * @return int
     */
    public function length()
    {
        return $this->client->zcard($this->key);
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
     * Returns the number of elements in the sorted set with a score between min and max.
     * @see http://redis.io/commands/zcount
     */
    public function countScoreBetween($min = '-inf', $max = '+inf')
    {
        return $this->client->scount($this->key, $min, $max);
    }

    public function removeAll()
    {
        $this->client->del($this->key);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->rangeWithScores());
    }

    public function highestScores($count = 1)
    {
        return $this->reverseRangeWithScores(0 , $count - 1);
    }

    public function highestScore()
    {
        $h = $this->highestScores(1);
        return array(key($h), current($h));
    }

    public function lowestScores($count = 1)
    {
        return $this->rangeWithScores(0, $count - 1);
    }

    public function lowestScore()
    {
        $l = $this->lowestScores(1);
        return array(key($l), current($l));
    }

    /**
     * Increments the score of member in the sorted set stored by increment.
     * @see http://redis.io/commands/zincrby
     */
    public function increment($increment, $member)
    {
        return $this->client->zincrby($this->key, $increment, $member);
    }

    protected function normalizeScores($results)
    {
        $range = array();
        foreach ($results as $result) {
            list ($member, $score) = $result;
            $range[$member] = $score;
        }

        return $range;
    }
}
