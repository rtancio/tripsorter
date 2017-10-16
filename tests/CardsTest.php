<?php

// use PHPUnit\Framework\TestCase;

require_once('src/TripSorter.php');
/**
 * @covers TripSorter
 */
class TripSorterTest extends PHPUnit_Framework_TestCase
{
 	private $tripsorter;
 
    protected function setUp()
    {
        $this->tripsorter = new TripSorter();
    }
 
    protected function tearDown()
    {
        $this->tripsorter = NULL;
    }
	
	public function testTripCards()
    {
		$this->tripsorter->scanList();
		$this->tripsorter->sortList();
		// $this->tripsorter->displayList();
        $this->assertEmpty($this->tripsorter->tripCards);
    }
	public function testTripList()
    {
		$this->tripsorter->scanList();
		$this->tripsorter->sortList();
		$return = $this->tripsorter->displayList();
        $this->assertNull($return);
    }
}