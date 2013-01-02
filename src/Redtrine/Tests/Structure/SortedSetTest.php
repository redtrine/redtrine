<?php

namespace Redtrine\Tests\Structure;

use Redtrine\Structure\SortedSet;
use Redtrine\Tests\RedtrineTestCase;

class SortedSetTest extends RedtrineTestCase
{
    /**
     * @var SortedSet
     */
    protected $set;

    public function setUp()
    {
        parent::setUp();

        $this->set = new SortedSet('theNameOfTheSet');
        $this->set->setClient($this->getRedisClient());
        $this->set->removeAll();
    }

    /**
     * @dataProvider getElementsWithScore
     */
    public function testAdd($element, $score)
    {
        $this->set->add($element);
        $this->assertTrue($this->set->contains($element));
    }

    /**
     * @dataProvider getElementsWithScore
     */
    public function testRemove($element, $score)
    {
        $this->set->add($element);
        $this->assertTrue($this->set->contains($element));

        $this->set->remove($element);
        $this->assertFalse($this->set->contains($element));
    }

    public function testExists()
    {
        $elements = $this->getRandomElementsWithScores();
        foreach ($elements as $element) {
            $this->set->add($element);
            $this->assertTrue($this->set->contains($element));
        }

        foreach ($elements as $element) {
            $this->assertTrue($this->set->exists($element));
        }

        foreach ($elements as $element) {
            $this->set->remove($element);
            $this->assertFalse($this->set->exists($element));
        }
    }

    public function testElements()
    {
        $elements = $this->getRandomElementsWithScores();

        foreach ($elements as $element) {
            $this->set->add($element);
            $this->assertTrue($this->set->contains($element));
        }

        $setElements = array_values($this->set->elements());
        sort($setElements);

        $elements = array_values($elements);
        sort($elements);

        $this->assertEquals($setElements, $elements);

        return $elements;
    }

    public function testRank()
    {
        $elements = $this->getRandomElementsWithScores();

        foreach ($elements as $element => $score) {
            $this->set->add($element, $score);
        }

        asort($elements);
        $rank = 0;

        foreach ($elements as $element => $score) {
            $elementRank = $this->set->rank($element);
            $this->assertTrue($elementRank >= $rank);
            $rank = $elementRank;
        }
    }

    /**
     * @dataProvider getElementsWithScore
     */
    public function testScore($element, $score)
    {
        $this->set->add($element, $score);
        $this->assertEquals($score, $this->set->score($element));
    }

    public function testRange()
    {
        $elements = $this->populateSortedSet();
        $this->assertEquals($this->set->length(), count($elements));

        $range = $this->set->range(0, -1);
        $this->assertEquals(count($elements), count($range));

        $range = $this->set->range(0, 4);
        $this->assertEquals(5, count($range));
    }

    public function testRangeWithScores()
    {
        $elements = $this->populateSortedSet();
        $this->assertEquals($this->set->length(), count($elements));

        $range = $this->set->rangeWithScores(0, -1);
        $this->assertEquals(count($elements), count($range));

        $range = $this->set->rangeWithScores(0, 4);
        $this->assertEquals(5, count($range));
    }

    public function testLenght()
    {
        $this->assertEquals(0, $this->set->length());
        $elements = $this->testElements();

        $this->assertEquals(count($elements), $this->set->length());
    }

    public function testIterator()
    {
        $this->populateSortedSet();
        $pos = 0;
        foreach ($this->set as $member => $score) {
            $this->assertTrue($this->set->contains($member));
            $this->assertEquals($this->set->score($member), $score);
            $this->assertEquals($pos, $this->set->rank($member));
            $pos++;
        }
        $this->assertEquals($this->set->length(), $pos);
    }

    public function testHighestScore()
    {
        $this->populateSortedSet();
        list($member, $score) = $this->set->highestScore();
        $this->assertEquals($this->set->length() - 1, $this->set->rank($member));
        $this->assertEquals($this->set->score($member), $score);
    }

    public function testLowestScore()
    {
        $this->populateSortedSet();
        list($member, $score) = $this->set->lowestScore();
        $this->assertEquals(0, $this->set->rank($member));
        $this->assertEquals($this->set->score($member), $score);
    }

    public function getElementsWithScore()
    {
        $result = array();
        foreach ($this->getRandomElementsWithScores() as $element => $score) {
            $result[] = array($element, $score);
        }

        return $result;
    }

    public function getRandomElementsWithScores()
    {
        $result = array();
        $total = 20;
        $score = rand(50, 100);;
        for ($i = 0; $i < $total; $i++) {
            $score += mt_rand(1, 100);
            $result[md5(uniqid(rand(), true))] = $score;
        }

        return $result;
    }

    protected function populateSortedSet()
    {
        $elements = $this->getRandomElementsWithScores();

        foreach ($elements as $element => $score) {
            $this->set->add($element, $score);
        }

        return $elements;
    }
}
