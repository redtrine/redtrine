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
     * Iterates N numHashes , creates crc32 Item&Hash Mod Size
     * Convert it to binary value
     * Iterates on every char , if are 1 uses setBit
     *
     * @param string $item
     */
    public function add($item)
    {
        $this->client->multi();

        for ($hashIndex = 0; $hashIndex < $this->getNumHashes(); $hashIndex++) {

            //binary representation
            $crc = decbin(crc32($item.$hashIndex) % $this->getSize());

            var_dump($crc);

            for ($i=0; $i < strlen($crc); $i++) {
                if ($crc[$i] === '1') {
                    $this->client->setbit($this->key, $i, 1);
                }
            }
        }
        $this->client->exec();
    }

    /**
     * Check if Item exists on Bloom Filter
     * Iterates N numHashes , creates crc32 Item&Hash Mod Size
     * Convert it to binary value
     * Iterates on every char , if are 1 uses getBit
     * If any of them reports 1 is true
     * Remember , this filter avoids false negatives
     *
     * @param string $item
     */
    public function exists($item)
    {
        $this->client->multi();
        for ($hashIndex = 0; $hashIndex < $this->getNumHashes(); $hashIndex++) {

            $crc = decbin(crc32($item.$hashIndex) % $this->getSize());

            for ($i=0; $i < strlen($crc); $i++) {
                if ($crc[$i] === '1') {
                    $this->client->getbit($this->key, $i);
                }
            }
        }
        $result = $this->client->exec();
        var_dump($result);

        //@TODO: Not clear how to return right result
        return !(array_sum($result) === 0);
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