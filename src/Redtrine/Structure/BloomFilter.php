<?php

namespace Redtrine\Structure;

/**
 * A Simple BloomFilter.
 * Bloomfilter is a probabilistic data structure used to determine
 * if an element is present in a set. There may be false positives,
 * but there cannot be false negatives.
 */
class BloomFilter extends Base
{
    public function __construct ($name, $size=100, $numHashes = 2)
    {
        parent::__construct($name);
        $this->size = $size;
        $this->numHashes = $numHashes;
    }
    /**
     * Number of hashes to perform. default is 2
     *
     * @var [type]
     */
    protected $numHashes;

    /**
     * size - Size of the bloom filter , default is 100 bits
     *
     * @var [type]
     */
    protected $size;

    /**
     * Add Item on Bloom Filter
     *
     * @param string $item
     */
    public function add($item)
    {
        $this->generateBitCode('set', $item);
    }

    /**
     * Check if key exists in a probabilistic way
     * Response es fully truth when response is FALSE
     *
     * @param string $item
     */
    public function exists($item)
    {
        $result = $this->generateBitCode('get', $item);

        return (array_sum($result)/count($result)) === 1;
    }

    /**
     * Check if Item exists on Bloom Filter
     * Iterates N numHashes , creates a single hash
     * Convert it to binary value
     * Iterates on every char , get/set associated Bits
     *
     * @param str $method Working Side
     * @param str $item Related Key
     * @return array
     */
    protected function generateBitCode($method, $item)
    {
        if (!in_array($method, array('get', 'set')))
            throw new \Exception("Method Not allowed");

        $this->client->multi();

        for ($hashIndex = 0; $hashIndex < $this->getNumHashes(); $hashIndex++) {

            $crc = decbin(crc32($item.'-'.$hashIndex) % $this->getSize());

            for ($i=0; $i < strlen($crc); $i++) {
                if ($crc[$i] === '1') {
                    if ($method === 'set')
                        $this->client->setbit($this->key, $i, 1);
                    if ($method === 'get')
                        $this->client->getbit($this->key, $i);
                }
            }
        }

        return $this->client->exec();
    }

    /**
     * Destroy bloomFilter
     */
    public function reset()
    {
        $this->client->del($this->key);
    }

    public function getNumHashes()
    {
        return $this->numHashes;
    }

    public function setNumHashes($numHashes)
    {
        $this->numHashes = $numHashes;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

}