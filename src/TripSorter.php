<?php

/**
 * Interface TransportationInterface
 *
 * 
 */

interface TranspoInterface
{
    /**
     * Return a message
     *
     * @return string
     */
    public function naturalize($ptFrom,$ptTo);
}

/**
 * Class Transpo
 *
 * 
 */

abstract class Transpo implements TranspoInterface {
	var $seatInfo;
	var $gateNum;
	var $baggageNum;
	var $rideName;
	
	function __construct($seat,$gate,$baggage,$ride) {
		// $this->name = $transponame;
		$this->seatInfo = $seat;
		$this->gateNum = $gate;
		$this->baggageNum = $baggage;
		$this->rideName = $ride;
	}
}

/**
 * Class Train
 *
 * 
 */

class Train extends Transpo {
	function naturalize($ptFrom, $ptTo) {
		$message = 'Take Train '. $this->rideName.' from '.$ptFrom. ' to '. $ptTo. '.';
		if($this->seatInfo != 'NA') {
			$message .= " Sit in ". $this->seatInfo;
		} else {
			$message .= ' No seat assignment.';
		}

		
		return $message;
	}
}

/**
 * Class Bus
 *
 * 
 */
class Bus extends Transpo {
	function naturalize($ptFrom, $ptTo) {
		$message = 'Take the '. $this->rideName.' from '.$ptFrom. ' to '. $ptTo. '.';
		if($this->seatInfo != 'NA') {
			$message .= " Sit in ". $this->seatInfo;
		} else {
			$message .= ' No seat assignment.';
		}

		
		return $message;
	}
}

/**
 * Class Plane
 *
 * 
 */
class Plane extends Transpo {
	function naturalize($ptFrom, $ptTo) {
		$message = 'From '. $ptFrom.', take flight '.$this->rideName. ' to '. $ptTo.'.';
		if($this->gateNum != 'NA') {
			$message .= ' Gate '.$this->gateNum. ',';
		} else {
			$message .= ' No gate information.';
		}
		
		if($this->seatInfo != 'NA') {
			$message .= " seat ". $this->seatInfo.'.';
		} else {
			$message .= ' No seat assignment.';
		}
		
		if($this->baggageNum != '') {
			$message .= "\nBaggage drop at ticket counter ".$this->baggageNum.".";
		}

		
		return $message;
	}
}
/**
 * Class TripSorter
 *
 * 
 */
class TripSorter {
	/**
	 * @var string
	*/
	var $csvFile =  './list/card1.csv';
	/**
	 * @var array
	*/
	var $tripCards;
	/**
	 * @var array
	*/
	var $sortedCards;
	
    /**
     * CSV file reader helper function
     *
     * @return int
     */
	function searchIdx($field, $value)
	{
	   foreach($this->tripCards as $key => $product)
	   {
		   // echo "search for: $value using field: $field".PHP_EOL;
	      if ( $product[$field] === $value )
	         return $key;
	   }
	   // echo "return false";
	   return -1;
	}
    /**
     * CSV file reader helper function
     *
     * @return array
     */
	function readCSV(){
		$file_handle = fopen($this->csvFile, 'r') or die("Unable to open csv file!");
		while (!feof($file_handle) ) {
			$line_of_text[] = fgetcsv($file_handle, 1024);
		}
		fclose($file_handle);
		return $line_of_text;
	}
    /**
     * Scan for the boarding cards
     *
     * 
     */
	function scanList() {
		// echo "csvFile: $csvFile".PHP_EOL;
		$csv = $this->readCSV();
		$this->tripCards = array();
		if(count($csv) > 0) {
			$rowcnt = 0;
			foreach($csv as $row) {
				if($rowcnt != 0 && count($row) > 0) {
					
					if(!empty($row[0])) {
						$this->tripCards[] = array('ptFrom'=>trim($row[0]),'ptTo'=>trim($row[1]),'mode'=>trim($row[2]),'seatNum'=>trim($row[3]),'gateNum'=>trim($row[4]),'beltNum'=>trim($row[5]),'ride'=>trim($row[6]),'tripClass'=>trim($row[7]));
					}
				}
				$rowcnt++;
			}
		}
		// print_r($this->tripCards).PHP_EOL;
	}
    /**
     * Sort the scanned boarding cards
     *
     * 
     */
	function sortList() {
		if(count($this->tripCards) > 0) 
		{
			// searching for destination
			$key = $this->searchIdx('tripClass','Destination');
			// echo "key: $key\n";
			if(!empty($this->tripCards[$key])) {
				$this->sortedCards[]=$this->tripCards[$key];
				// print_r($this->sortedCards).PHP_EOL;
			
			
				$destPt1 = $this->tripCards[$key]['ptFrom'];
				array_splice($this->tripCards, $key, 1);
				// print_r($this->tripCards).PHP_EOL;
				$cardsCnt = count($this->tripCards);
				for($i = 0; $i < $cardsCnt; $i++) {
					// if($i==0) {
						// $lastused = 'ptTo';
						// $key = $this->searchIdx('ptTo',$destPt1);
					// }  else {
						$key = $this->searchIdx('ptTo',$destPt1);	
					// }
					/*
					else {
						if($lastused == 'ptTo') {
							$key = $this->searchIdx('ptFrom',$destPt1);	
						} else {
							$key = $this->searchIdx('ptTo',$destPt1);	
						}
					}
					*/
				
					// echo "key: $key\n";
					if(!empty($this->tripCards[$key])) {
						$this->sortedCards[]=$this->tripCards[$key];
						$destPt1 = $this->tripCards[$key]['ptFrom'];
						array_splice($this->tripCards, $key, 1);
					} else {
						break;
					}
					
				}
			}
			
			// print_r($this->sortedCards).PHP_EOL;
			// echo ""
			// print_r($this->tripCards).PHP_EOL;
			
			$finallist = array_reverse($this->sortedCards);
			// print_r($finallist).PHP_EOL;
			$this->sortedCards = $finallist;
		}
	}

    /**
     * Return the final list of the trip
     *
     * @return string
     */
	function displayList() {
		if(count($this->tripCards) > 0) {
			// echo "Sorry could not provide the list. Found broken chain between legs.".PHP_EOL;
		} else {
			// echo "sorted Cards: ".PHP_EOL;
			// print_r($this->sortedCards).PHP_EOL;
			$listmessage = '';
			$i = 1;
			foreach($this->sortedCards as $row) {
				$ptFrom = $row['ptFrom'];
				$ptTo = $row['ptTo'];
				$mode = $row['mode'];
				$seat = $row['seatNum'];
				$gate = $row['gateNum'];
				$baggage = $row['beltNum'];
				$ride = $row['ride'];
				if($mode == 'Bus') {
					$listTranspo = new Bus($seat,$gate,$baggage,$ride);
				} else if($mode == 'Train') {
					$listTranspo = new Train($seat,$gate,$baggage,$ride);
				} else if($mode == 'Plane') {
					$listTranspo = new Plane($seat,$gate,$baggage,$ride);
				}
				if(isset($listTranspo)) {
					$transpoNaturalize = $listTranspo->naturalize($ptFrom,$ptTo);
				}
				$listmessage .= "$i. $transpoNaturalize\n";
				$i++;
			}
			$listmessage .= "$i. You have arrived at your final destination.\n";
			echo $listmessage.PHP_EOL;
		}
	}
	
}	
// class TripSorter {
		
// }
/*
$madBar = new Train('45B','NA','NA', '78A');
$thistrip = $madBar->naturalize('Madrid','Barcelona');
echo $thistrip. PHP_EOL;

$madBar = new Plane('3A','45B','344', 'SK455');
$thistrip = $madBar->naturalize('Gerona Airport','Stockholm');
echo $thistrip. PHP_EOL;

$madBar = new Bus('NA','NA','NA', 'airport bus');
$thistrip = $madBar->naturalize('Barcelona','Gerona Airport');
echo $thistrip. PHP_EOL;
*/

	
?>