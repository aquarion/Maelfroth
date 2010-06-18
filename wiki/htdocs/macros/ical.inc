<?PHP
// $lang['l_error_invalidcal'] = "Invalid Cal";


function error($text = "Error", $problem = "Problem"){
	error_log(getcwd()." - ".$text." - ".$problem);
	die("<b>$text</b>");
}


#$gridLength                      = '15';
$GLOBALS['master_array'] = array();
$GLOBALS['overlap_array'] = array();

include("cal_data/timezones.php");

// print_r($tz_array);

include("cal_data/date_functions.php");
// include("cal_data/more_functions.php");
include("cal_data/overlapping_events.php");

include("cal_data/cal.php");
include("cal_data/english.inc.php");

// if (!file_exists($var)){

// 	echo "Cannot run $var";
// } else {

if (true){

	echo $var;

	parse_ical($var, $tz_array, $gridLength);
	/*[[CAL]]
	
	[[CAL|2005-04-07|Theory Test]]*/
	
	$out = "[[CAL]]\n\n";
	
	foreach($GLOBALS['master_array'] as $date => $dateData){
		if (!is_numeric($date) || $date < 0){
	// 		echo "$date $dateData<br/>";
	
			continue;
		}
	// 	echo "$date<br/>";
		$date =	substr($date, 0,4)."-".substr($date, 4,2)."-".substr($date, 6,2);
	
		foreach($dateData as $time => $timeData){
	
			foreach($timeData as $id => $event){
				if ($time < 0){
					$time = "All Day Event";
				} else {
					$time = substr($event['event_start'], 0,2)
						.":".substr($event['event_start'], 2,2)
						." - ".substr($event['event_end'], 0,2).":"
						.substr($event['event_end'], 2,2);
				}
	
				$event['event_text'] = stripslashes(urldecode($event['event_text']));	
	
				$out .= sprintf("* [[CAL|%s|%s]] %s",
					$date, $event['event_text'], $date." ".$time." ".$event['event_text'])."\n";
			}
			
		}
		
	}
	echo "\n\n".$out."\n\n";
	// echo "<pre>".$out."\n\n".print_r($master_array,1)."</pre>";
}
?>