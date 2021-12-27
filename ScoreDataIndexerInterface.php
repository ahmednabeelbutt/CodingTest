<?php

interface ScoreDataIndexerInterface {
 
	/**
	* Returns count of users having score withing the interval.
	*
	* @param int $rangeStart
	* @param int $rangeEnd
	* @return int
	*/
	public function getCountOfUsersWithinScoreRange(int $rangeStart, int $rangeEnd): int;

	/**
	* Returns count of users meet input condition.
	*
	* @param string $region
	* @param string $gender
	* @param bool $hasLegalAge
	* @param bool $hasPositiveScore
	* @return int
	*/
	public function getCountOfUsersByCondition(string $region, string $gender, bool $hasLegalAge, bool $hasPositiveScore): int;

}

class ScoreDataIndexer implements ScoreDataIndexerInterface {

	public function getCountOfUsersWithinScoreRange(int $rangeStart, int $rangeEnd): int {

		$csv_data = array(self::load_csv_file());
		$count = 0;

		foreach ($csv_data as $data) {
			foreach ($data as $key => $value) {
				if (in_array((int)$value['Score'], range($rangeStart, $rangeEnd))) {
					$count = $count + 1;
				}
			}
		}

		return $count;
	}
	
	public function getCountOfUsersByCondition(string $region, string $gender, bool $hasLegalAge, bool $hasPositiveScore): int {

		$csv_data = array(self::load_csv_file());
		$count = 0;

        foreach ($csv_data as $data) {
			foreach ($data as $key => $value) {
				$legal_age = (int)$value['Age'] > 0 && (int)$value['Age'] < 100 ? true : false;
				$positive_score = (int)$value['Score'] > 0 ? true : false;
			
				if ($hasLegalAge == $legal_age && $hasPositiveScore == $positive_score && $value['Region'] == $region && $value['Gender'] == $gender) {
					$count = $count + 1;
				}
			}
        }

		return $count;
	}

	public static function load_csv_file() {

		$csv_data = [];

		if (($handle = fopen("test.csv", "r")) !== false) {                 
		    if (($data = fgetcsv($handle, 1000, ";")) !== false) {        
		        $keys = $data;                                             
		    }
		    while (($data = fgetcsv($handle, 1000, ";")) !== false) {     
		        $csv_data[] = array_combine($keys, $data);              
		    }
		    
		    fclose($handle); 

		    return $csv_data;                                            
		}
	}
}

$index = new ScoreDataIndexer();
$countByScore = $index->getCountOfUsersWithinScoreRange(-40, 0);
$countByCondition = $index->getCountOfUsersByCondition('CA', 'w', true, true); 