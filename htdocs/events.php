<?PHP

$title = "Events";
include("header.php");

$datecache = '';
?>


<div id="content">

<h1>Events</h1>

<p class="intro">This is the list of events Lampstand currently knows about. If you want another thing to be on this list, email Aquarion (aquarion at maelfroth fullstop org) with the details. To ask Lampstand how long until something is, say "Lampstand: How long until (event title)?" (Event title is the bit in <i>italics</i>). If you ask "How long until (event type)?" (The bit in bold) it will give you the next event of that class, so "How long until Maelstrom?" will give you the time until the next event.
</p>

<p>You can subscribe to this as a calendar in <a href="http://www.google.com/calendar/render?cid=apn6om2v7p1d77prbsj2i3aia9gq1f84%40import.calendar.google.com">Google Calendar</a>, or in your own <a href="http://www.maelfroth.org/events.ics.php">Calendar software</a> if you have any installed.</p>

<h1>The Future</h1>
<?PHP

$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

$query = "select *, unix_timestamp(datetime) as datetime_epoch, unix_timestamp(datetime_end) as datetime_end_epoch from events where datetime > now() order by datetime";

$result = $db->query($query);
if(!$result){
	print_r($db->errorInfo());
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)){
	echo "<li><strong>{$row['class']}</strong>: <i>{$row['description']}</i> - ".date('l d\<\s\u\p\>S\<\/\s\u\p\> F Y', $row['datetime_epoch'])." </li>";
}
#-----------------------------
?><h1>The Past</h1><?PHP

$query = "select *, unix_timestamp(datetime) as datetime_epoch, unix_timestamp(datetime_end) as datetime_end_epoch from events where datetime < now() order by class, datetime";

$result = $db->query($query);


if(!$result){
	print_r($db->errorInfo());
}

while ($row = $result->fetch(PDO::FETCH_ASSOC)){
	echo "<li><strong>{$row['class']}</strong>: <i>{$row['description']}</i> - ".date('l d\<\s\u\p\>S\<\/\s\u\p\> F Y', $row['datetime_epoch'])." </li>";
}
?>
</div>

</body>
</html>
