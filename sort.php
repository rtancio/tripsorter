<?php
	
require_once('src/TripSorter.php');

$tripsort = new TripSorter();
$tripsort->scanList();
$tripsort->sortList();
$tripsort->displayList();

?>