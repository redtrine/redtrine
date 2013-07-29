<?php
namespace Redtrine\Structure;

class RateLimit extends Base
{
    /**
     * Bucket interval - how many seconds each bucket represents
     * @var [type]
     */
    protected $bucketInterval;

    /**
     * Bucket span - in our circle analogy, the bucket span is the total size of the circle (in seconds)
     * @var [type]
     */
    protected $bucketSpan;

    /**
     * Subject expiry - the amount of (inactive) seconds before a subject's time buckets expire
     * @var [type]
     */
    protected $subjectExpiry;

    /**
     * (derived) Bucket count = Bucket span / Bucket interval
     * @var [type]
     */
    protected $bucketCount;

    /**
     * [__construct description]
     * @param [type]  $name           [description]
     * @param integer $bucketInterval [description]
     * @param integer $bucketSpan     [description]
     * @param integer $subjectExpiry  [description]
     */
    public function __construct($name, $bucketInterval = 5, $bucketSpan = 600, $subjectExpiry = 1200)
    {
        parent::__construct($name);
        $this->bucketInterval = $bucketInterval;
        $this->bucketSpan = $bucketSpan;
        $this->bucketCount = (int)round($this->bucketSpan / $this->bucketInterval);
        $this->subjectExpiry = $subjectExpiry;
    }


    /**
     * Add item or items array to rateLimit structure
     *
     * @param string $subject SubKey
     * @param int $time origin timestamp, used on testing
     */
    public function add($subject, $time = null)
    {
        $bucket = $this->getBucket($time);
        $this->addFromBucket($subject, $bucket);

        return $this;
    }

    /**
     * Add item or items array to destination bucket
     *
     * @param string $subject SubKey
     * @param int $bucket origin bucket, used on testing
     */
    protected function addFromBucket($subject, $bucket)
    {
        $subject = (is_array($subject))? $subject: array($subject);
        $this->client->multi();

        foreach ($subject as $item) {
            $this->addItem($item, $bucket);
        }

        $this->client->exec();
    }

    /**
     * Add single item to destination bucket
     *
     * @param string $subject SubKey
     * @param int $bucket origin bucket, used on testing
     */
    protected function addItem($item, $bucket)
    {
        $itemKey = $this->key.':'.$item;
        //Increment the current bucket
        $this->client->hincrby($itemKey, $bucket, 1);

        //Clear the buckets ahead
        $this->client->hdel($itemKey, ($bucket + 1) % $this->bucketCount);
        $this->client->hdel($itemKey, ($bucket + 2) % $this->bucketCount);

        // Renew the key TTL
        $this->client->expire($itemKey, $this->subjectExpiry);
    }

    /**
     * Count the number of times the subject has performed an action in the last interval seconds.
     *
     * @param string $subject SubKey
     * @param int $interval seconds interval to count
     * @param int $time origin timestamp, used on testing
     * @return int result count
     */
    public function count($subject, $interval, $time = null)
    {
        $bucket = $this->getBucket($time);

        return $this->countFromBucket($subject, $interval, $bucket);
    }

    /**
     * Count the number of times the subject has performed an action in the bucket
     *
     * @param string $subject SubKey
     * @param int $interval seconds interval to count
     * @param int $bucket origin bucket
     * @return int result count
     */
    protected function countFromBucket($subject, $interval, $bucket)
    {
        $subject = $this->key.':'.$subject;
        $count = (int)floor($interval / $this->bucketInterval);

        $this->client->multi();

        //Get the counts from the previous `count` buckets
        while ($count--) {
            $this->client->hget($subject, ($bucket-- + $this->bucketCount) % $this->bucketCount);
        }

        $result = $this->client->exec();

        //Add up the counts from each bucket
        return array_sum($result);
    }

    /**
     * An alias for add(subject) and count(subject, interval)
     *
     * @param string $subject SubKey
     * @param int $interval seconds interval to count
     * @return int result count
     */
    public function addCount($subject, $interval)
    {
        $subject = (is_array($subject))? $subject: array($subject);
        $this->client->multi();

        $bucket = $this->getBucket();
        foreach ($subject as $item) {
            $this->addItem($item, $bucket);
        }

        $count = (int)floor($interval / $this->bucketInterval);
        foreach (array_unique($subject) as $item) {
            $item = $this->key.':'.$item;
            $itemBucket = $bucket;
            for ($i=1; $i <= $count ; $i++) {
                $this->client->hget($item, ($itemBucket-- + $this->bucketCount) % $this->bucketCount);
            }

        }

        $result = $this->client->exec();

        return $this->parseMultiResponse($result, $subject, $count);
    }

    /**
     * Parse Redis response
     * from response its remove add operations,
     * leaves only hget values , sumarized on associated keys
     *
     * @param array $result redis response
     * @param array $subject items
     * @param int $count  number of buckets
     * @return array item keys , total on value
     */
    protected function parseMultiResponse($result, $subject, $count)
    {
        $totalResponse = $count*count(array_unique($subject));
        $result = array_slice($result, -$totalResponse, $totalResponse);

        $pointer = 0;
        $response = array();
        foreach (array_unique($subject) as $item) {
            $response[$item] = array_sum(array_slice($result, $pointer*$count , $count));
            ++$pointer;
        }

       return $response;
    }

    /**
     * Destroy RateLimit Structure
     */
    public function reset()
    {
        $this->client->del($this->key);
    }

    /**
     * Get the bucket associated with the current time `floor((timestamp % bucket span) / bucket interval)`
     *
     * @return int Destination Bucket
     */
    protected function getBucket($time = null)
    {
        $time = (is_null($time)) ? time(): $time;

        return (int)floor(($time % $this->bucketSpan) / $this->bucketInterval);
    }
}