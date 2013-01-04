<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException,
    Behat\Behat\Event\SuiteEvent,
    Behat\Behat\Event\FeatureEvent;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Predis\Client;
use Redtrine\Structure\SortedSet;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    protected $redis;
    protected $set;
    protected $key;
    protected $score;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /** @BeforeFeature */
    public static function setupFeature(FeatureEvent $event)
    {
        try {
            $redis = new Client('tcp://127.0.0.1:6379');
            $redis->select(15);
            $redis->flushdb();
        } catch (\Exception $e) {
            echo "Redis Server is needed to test features!".PHP_EOL;
            exit;
        }
    }

    /**
     * @Given /^I have a working Redis Client$/
     */
    public function iHaveAWorkingRedisClient()
    {
        $this->redis = new Client('tcp://127.0.0.1:6379');
        $this->redis->select(15);

        AssertTrue($this->redis->isConnected());
    }

    /**
     * @Given /^I have a sorted set called "([^"]*)"$/
     */
    public function iHaveASortedSetCalled($arg1)
    {
        $this->set = new SortedSet($arg1);
        $this->set->setClient($this->redis);
    }

    /**
     * @Then /^the sorted has (\d+) items$/
     */
    public function theSortedHasItems($arg1)
    {
        $this->assertTotal($arg1);
    }

    /**
     * @Given /^the sorted has "([^"]*)" items$/
     */
    public function theSortedHasItems2($arg1)
    {
        $this->assertTotal($arg1);
    }

    protected function assertTotal($arg1)
    {
        AssertEquals($this->set->length(), $arg1);
    }
   /**
     * @Given /^I have a user with (\d+)$/
     */
    public function iHaveAUserWith($arg1)
    {
        $this->key = $arg1;
    }

    /**
     * @Given /^the user has a score of (\d+)$/
     */
    public function theUserHasAScoreOf($arg1)
    {
        $this->score = $arg1;
    }

    /**
     * @When /^I add an element with score$/
     */
    public function iAddAnElementWithScore()
    {
        $this->set->add($this->key, $this->score);
    }

    /**
     * @Given /^the Sorted Set contains (\d+)$/
     */
    public function theSortedSetContains($arg1)
    {
        AssertTrue($this->set->exists($arg1));
        $this->key = $arg1;
    }

    /**
     * @Given /^has a score of (\d+)$/
     */
    public function hasAScoreOf($arg1)
    {
        AssertEquals($this->set->score($this->key), $arg1);
    }


}
