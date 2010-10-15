<?PHP

$filterquery = "";
if(isset($_GET['filter'])){
	$filter = explode("|",$_GET['filter']);
	foreach ($filter as $index => $value){
		$filter[$index] = addslashes($value);
	}

	$filterquery = " AND class in (\"". implode($filter, '", "')."\")";
}


	header("Content-Type: text/calendar");
	#header("Content-Type: text/plain");
$header = 'BEGIN:VCALENDAR
PRODID:-//Aquarion//Lampstand 1.1//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:Lampstand Events
X-WR-TIMEZONE:Europe/London
X-WR-CALDESC:This is the list of events that Lampstand knows about.
BEGIN:VTIMEZONE
TZID:Europe/London
X-LIC-LOCATION:Europe/London
BEGIN:DAYLIGHT
TZOFFSETFROM:+0000
TZOFFSETTO:+0100
TZNAME:BST
DTSTART:19700329T010000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0100
TZOFFSETTO:+0000
TZNAME:GMT
DTSTART:19701025T020000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE
';

echo $header;

$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

$date = date("Y-m-d", time() - (60*60*24*365) );

$query = "select *, unix_timestamp(datetime) as datetime_epoch, unix_timestamp(datetime_end) as datetime_end_epoch from events where datetime > $date $filterquery order by datetime";


$result = $db->query($query);

function unixToiCal($uStamp = 0, $tzone = 0.0) {
    $uStampUTC = $uStamp + ($tzone * 3600);       
    $stamp  = date("Ymd\THis\Z", $uStampUTC);
    return $stamp;       
} 

while ($row = $result->fetch(PDO::FETCH_ASSOC)){

echo "BEGIN:VEVENT\n";

# If no end time, one day event.
if (!$row['datetime_end']) {
	echo "DTSTART;VALUE=DATE:".date('Ymd', $row['datetime_epoch'])."\n";
	echo "DTEND;VALUE=DATE:".date('Ymd', $row['datetime_epoch'] )."\n";
	
# if datetime is 00:00 and end_time is 23:59, multiday event. 
} elseif ( 
		date('%H:%i', $row['datetime_epoch']) == '00:00'
		&& date('%H:%i', $row['datetime_end_epoch']) == '23:59'
	 ){
	echo "DTSTART;VALUE=DATE:".date('Ymd', $row['datetime_epoch'] )."\n";
	echo "DTEND;VALUE=DATE:".date('Ymd', $row['datetime_end_epoch'] )."\n";

# If otherwise normal event
} else {
	echo "DTSTART:".unixToiCal($row['datetime_epoch'])."\n";
	echo "DTEND:"  .unixToiCal($row['datetime_end_epoch'])."\n";
}

echo "DTSTAMP:".unixToiCal(time())."\n";
echo "UID:lampstandEvent".md5($row['description'])."@lampstand.maelfroth.org\n";
echo "CLASS:PUBLIC\n";
#echo "CREATED:20080924T073953Z\n";
#echo "LAST-MODIFIED:20080924T074007Z\n";
echo "SEQUENCE:0\n";
echo "STATUS:CONFIRMED\n";
echo "SUMMARY:".$row['class']." - ".$row['description']."\n";
echo "TRANSP:TRANSPARENT\n";
echo "END:VEVENT\n";

}
#-----------------------------
?>
END:VCALENDAR
